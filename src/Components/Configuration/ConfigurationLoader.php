<?php

namespace PHPUnuhi\Configuration;

use Exception;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Configuration\Services\ConfigurationValidator;
use PHPUnuhi\Configuration\Services\FilterLoader;
use PHPUnuhi\Configuration\Services\LocalesLoader;
use PHPUnuhi\Configuration\Services\ProtectionLoader;
use PHPUnuhi\Configuration\Services\RulesLoader;
use PHPUnuhi\Configuration\Services\StyleLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Loaders\Xml\XmlLoaderInterface;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class ConfigurationLoader
{
    use XmlTrait;


    /**
     * @var XmlLoaderInterface
     */
    private $xmlLoader;

    /**
     * @var FilterHandler
     */
    private $filterHandler;

    /**
     * @var ConfigurationValidator
     */
    private $configValidator;

    /**
     * @var LocalesLoader
     */
    private $localesLoader;

    /**
     * @var RulesLoader
     */
    private $rulesLoader;

    /**
     * @var StyleLoader
     */
    private $styleLoader;

    /**
     * @var FilterLoader
     */
    private $filterLoader;

    /**
     * @var ProtectionLoader
     */
    private $protectionLoader;


    /**
     * @param XmlLoaderInterface $xmlLoader
     */
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
    }


    /**
     * @param string $rootConfigFilename
     * @throws ConfigurationException
     * @throws Exception
     * @return Configuration
     */
    public function load(string $rootConfigFilename): Configuration
    {
        $rootXmlSettings = $this->xmlLoader->loadXML($rootConfigFilename);

        $rootConfigDir = dirname($rootConfigFilename) . '/';

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
            $suites = $this->loadTranslations($fileXmlNode, $fullFilename);

            $allSuites = array_merge($allSuites, $suites);
        }

        # create and validate the configuration object
        $config = new Configuration($allSuites);
        $this->configValidator->validateConfig($config);

        return $config;
    }


    /**
     * @param SimpleXMLElement $rootNode
     * @return void
     */
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
     * @param SimpleXMLElement $rootNode
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
     * @param SimpleXMLElement $rootNode
     * @param string $configFilename
     * @throws Exception
     * @return TranslationSet[]
     */
    private function loadTranslations(SimpleXMLElement $rootNode, string $configFilename): array
    {
        $hasTranslationSets = ($rootNode->translations->children() !== null);

        if (!$hasTranslationSets) {
            throw new ConfigurationException('Invalid configuration! No translation node has been found!');
        }

        $nodeTranslations = $rootNode->translations->children();

        if (count($nodeTranslations) <= 0) {
            throw new ConfigurationException('Invalid configuration! No translation-sets have been found!');
        }

        $suites = [];

        /** @var SimpleXMLElement $xmlSet */
        foreach ($nodeTranslations as $xmlSet) {
            $setName = trim((string)$xmlSet['name']);
            $nodeFormat = $xmlSet->format;
            $nodeProtection = $xmlSet->protect;
            $nodeLocales = $xmlSet->locales;
            $nodeFilter = $xmlSet->filter;
            $nodeStyles = $xmlSet->styles;
            $nodeRules = $xmlSet->rules;

            # default values
            $setFormat = 'json';
            $setProtection = new Protection();
            $setAttributes = [];
            $setLocales = [];
            $setFilter = new Filter();
            $casingStyles = [];
            $rules = [];

            if ($nodeFormat !== null) {
                $formatData = $this->parseFormat($nodeFormat);
                $setFormat = $formatData['format'];
                $setAttributes = $formatData['attributes'];
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
            }

            if ($nodeRules !== null) {
                $rules = $this->rulesLoader->loadRules($nodeRules);
            }

            $set = new TranslationSet(
                $setName,
                $setFormat,
                $setProtection,
                $setLocales,
                $setFilter,
                $setAttributes,
                $casingStyles,
                $rules
            );

            $storage = StorageFactory::getInstance()->getStorage($set);


            # some storages do not support filtering
            # so make sure to throw an exception if we have a filter config.
            # this is because filters are also used in imports (at least affects imports)
            # which means that they would lead to removed keys on FILE-type storages.
            if (!$storage->supportsFilters() && $set->getFilter()->hasFilters()) {
                throw new ConfigurationException('Filters are not allowed for storage format: ' . $setFormat);
            }


            # now iterate through our locales
            # and load the translation files for it
            $storage->loadTranslationSet($set);

            # remove fields that must not be existing
            # because of our allow or exclude list
            $this->filterHandler->applyFilter($set);

            $suites[] = $set;
        }

        return $suites;
    }


    /**
     * @param SimpleXMLElement $rootFormat
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
