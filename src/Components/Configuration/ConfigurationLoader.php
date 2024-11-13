<?php

declare(strict_types=1);

namespace PHPUnuhi\Configuration;

use Exception;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Configuration\Services\ConfigurationValidator;
use PHPUnuhi\Configuration\Services\CoverageLoader;
use PHPUnuhi\Configuration\Services\FilterLoader;
use PHPUnuhi\Configuration\Services\LocalesLoader;
use PHPUnuhi\Configuration\Services\LocalesPlaceholderProcessor;
use PHPUnuhi\Configuration\Services\ProtectionLoader;
use PHPUnuhi\Configuration\Services\RulesLoader;
use PHPUnuhi\Configuration\Services\StyleLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\LazyTranslationSet;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Loaders\Xml\XmlLoaderInterface;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class ConfigurationLoader
{
    use XmlTrait;


    private XmlLoaderInterface $xmlLoader;

    private FilterHandler $filterHandler;

    private ConfigurationValidator $configValidator;

    private LocalesLoader $localesLoader;

    private RulesLoader $rulesLoader;

    private StyleLoader $styleLoader;

    private FilterLoader $filterLoader;

    private ProtectionLoader $protectionLoader;

    private CoverageLoader $coverageLoader;

    /**
     * @var TranslationSetCoverage[]
     */
    private array $bufferTranslationSetCoverages = [];

    private LocalesPlaceholderProcessor $localesPlaceholderProcessor;


    public function __construct(XmlLoaderInterface $xmlLoader)
    {
        $this->xmlLoader = $xmlLoader;

        $this->filterHandler = new FilterHandler();
        $this->configValidator = new ConfigurationValidator();
        $this->localesLoader = new LocalesLoader();
        $this->rulesLoader = new RulesLoader();
        $this->styleLoader = new StyleLoader();
        $this->filterLoader = new FilterLoader();
        $this->protectionLoader = new ProtectionLoader();
        $this->coverageLoader = new CoverageLoader();
        $this->localesPlaceholderProcessor = new LocalesPlaceholderProcessor();
    }


    /**
     * @throws Exception
     * @throws ConfigurationException
     */
    public function load(string $rootConfigFilename): Configuration
    {
        $this->bufferTranslationSetCoverages = [];

        $rootXmlSettings = $this->xmlLoader->loadXML($rootConfigFilename);

        $rootConfigDir = dirname($rootConfigFilename) . '/';

        # load our bootstrap file if provided
        $bootstrap = $this->getAttribute('bootstrap', $rootXmlSettings)->getValue();

        if ($bootstrap !== '' && $bootstrap !== '0') {
            $bootstrapOriginal = $bootstrap;
            $bootstrap = (string)realpath($rootConfigDir . '/' . $bootstrap);

            if (!file_exists($bootstrap)) {
                throw new ConfigurationException('Bootstrap file not found: ' . $bootstrapOriginal);
            }

            require_once $bootstrap;
        }

        # we might have sub imports in files with <imports>
        # so we load the list of files to import
        # and also add our root config file.
        $importFiles = $this->loadImports($rootXmlSettings);
        $importFiles[] = basename($rootConfigFilename);

        $allSuites = [];

        # now iterate through all our files and process
        # every file independently, because it might have some content in it
        foreach ($importFiles as $file) {
            $fullFilename = $rootConfigDir . $file;

            $fileXmlNode = $this->xmlLoader->loadXML($fullFilename);

            # load ENV variables
            $this->loadPHPEnvironment($fileXmlNode);

            # load translation-sets
            $suites = $this->loadTranslationSets($fileXmlNode, $fullFilename);

            $allSuites = array_merge($allSuites, $suites);
        }

        # create and validate the configuration object
        $config = new Configuration($allSuites);

        # ------------------------------------------------------------------------------------

        $coverageNode = $rootXmlSettings->coverage;
        $coverage = $this->coverageLoader->loadGlobalCoverage($coverageNode);

        foreach ($this->bufferTranslationSetCoverages as $name => $setCoverage) {
            $coverage->addTranslationSetCoverage($name, $setCoverage);
        }

        $config->setCoverage($coverage);

        # ------------------------------------------------------------------------------------

        if (count($config->getTranslationSets()) <= 0) {
            throw new ConfigurationException('Invalid configuration! No translation-sets have been found!');
        }

        $this->configValidator->validateConfig($config);

        return $config;
    }


    private function loadPHPEnvironment(SimpleXMLElement $rootNode): void
    {
        $phpNodeValues = $rootNode->php->children();

        if ($phpNodeValues === null) {
            return;
        }

        if ($rootNode->php->env === null) {
            return;
        }

        foreach ($rootNode->php->env as $xmlSet) {
            $name = trim((string)$xmlSet['name']);
            $value = trim((string)$xmlSet['value']);
            putenv("{$name}={$value}");
        }
    }

    /**
     * @return array<mixed>
     */
    private function loadImports(SimpleXMLElement $rootNode): array
    {
        $imports = [];

        if ($rootNode->imports === null) {
            return [];
        }

        foreach ($rootNode->imports as $importNode) {
            // if there is more than one import, we have to iterate through them
            if ($importNode->import->count() > 1) {
                foreach ($importNode->import as $import) {
                    $resource = $this->getAttribute('resource', $import);
                    $imports[] = $resource->getValue();
                }
            } else {
                $resource = $this->getAttribute('resource', $importNode->import);
                $imports[] = $resource->getValue();
            }
        }

        return $imports;
    }

    /**
     * @throws Exception
     * @return TranslationSet[]
     */
    private function loadTranslationSets(SimpleXMLElement $rootNode, string $configFilename): array
    {
        $hasTranslationSets = ($rootNode->translations->children() !== null);

        # we might have different import-based configuration files
        # and it can be that the root file might not have a translation
        # so we just use an empty array in that case
        /** @var SimpleXMLElement[] $xmlTranslationSets */
        $xmlTranslationSets = $hasTranslationSets ? $rootNode->translations->children() : [];

        $foundSets = [];

        foreach ($xmlTranslationSets as $xmlSet) {
            $setName = trim((string)$xmlSet['name']);
            $nodeFormat = $xmlSet->format;
            $nodeProtection = $xmlSet->protect;
            $nodeLocales = $xmlSet->locales;
            $nodeFilter = $xmlSet->filter;
            $nodeStyles = $xmlSet->styles;
            $nodeRules = $xmlSet->rules;
            $nodeCoverage = $xmlSet->coverage;

            # default values
            $setFormat = 'json';
            $setProtection = new Protection();
            $setAttributes = [];
            $setLocales = [];
            $setFilter = new Filter();
            $casingStyles = [];
            $ignoredKeys = [];
            $rules = [];
            $setCoverage = null;

            if ($nodeFormat !== null) {
                $formatData = $this->parseFormat($nodeFormat);
                $setFormat = $formatData['format'];
                $setAttributes = $formatData['attributes'];

                /** @var Attribute $attribute */
                foreach ($setAttributes as $attribute) {
                    if ($attribute->getName() === 'file') {
                        $fixedPath = $this->localesPlaceholderProcessor->buildFullPath($attribute->getValue(), $configFilename);
                        $attribute->setValue($fixedPath);
                    }
                }
            }

            if ($nodeProtection !== null) {
                $setProtection = $this->protectionLoader->loadProtection($nodeProtection);
            }

            if ($nodeFilter !== null) {
                $setFilter = $this->filterLoader->loadFilter($nodeFilter);
            }

            if ($nodeLocales !== null) {
                $setLocales = $this->localesLoader->loadLocales($nodeLocales, $configFilename);
            }

            if ($nodeStyles !== null) {
                $casingStyles = $this->styleLoader->loadStyles($nodeStyles);
                $ignoredKeys = $this->styleLoader->loadIgnoredKeys($nodeStyles);
            }

            if ($nodeRules !== null) {
                $rules = $this->rulesLoader->loadRules($nodeRules);
            }

            if ($nodeCoverage !== null) {
                $setCoverage = $this->coverageLoader->loadTranslationCoverage($nodeCoverage);
            }

            $set = new LazyTranslationSet(
                $setName,
                $setFormat,
                $setProtection,
                $setLocales,
                $setFilter,
                $setAttributes,
                new CaseStyleSetting($casingStyles, $ignoredKeys),
                $rules
            );

            if ($setCoverage instanceof TranslationSetCoverage) {
                $this->bufferTranslationSetCoverages[$set->getName()] = $setCoverage;
            }


            $storage = StorageFactory::getInstance()->getStorage($set);


            # some storages do not support filtering
            # so make sure to throw an exception if we have a filter config.
            # this is because filters are also used in imports (at least affects imports)
            # which means that they would lead to removed keys on FILE-type storages.
            if (!$storage->supportsFilters() && $set->getFilter()->hasFilters()) {
                throw new ConfigurationException('Filters are not allowed for storage format: ' . $setFormat);
            }

            # We have to clone the storage as the object get reconfigured for every translation set
            $set->setStorage(clone $storage, $this->filterHandler);

            $foundSets[] = $set;
        }

        return $foundSets;
    }


    /**
     * @throws ConfigurationException
     * @return array<mixed>
     */
    private function parseFormat(SimpleXMLElement $rootFormat): array
    {
        $children = get_object_vars($rootFormat);

        if (count($children) <= 0) {
            throw new ConfigurationException('No format provided');
        }

        $format = '';
        $setAttributes = '';

        foreach ($children as $formatTag => $formatElement) {
            if ($formatTag === '@attributes') {
                continue;
            }

            $format = $formatTag;
            $setAttributes = $this->getAttributes($formatElement);
        }

        return [
            'format' => $format,
            'attributes' => $setAttributes,
        ];
    }
}
