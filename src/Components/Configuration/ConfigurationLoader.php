<?php

namespace PHPUnuhi\Configuration;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use SimpleXMLElement;


class ConfigurationLoader
{

    /**
     * @var FilterHandler
     */
    private $filterHandler;


    /**
     *
     */
    public function __construct()
    {
        $this->filterHandler = new FilterHandler();
    }


    /**
     * @param string $configFilename
     * @return Configuration
     * @throws ConfigurationException
     */
    public function load(string $configFilename)
    {
        $xmlString = (string)file_get_contents($configFilename);
        $xmlSettings = simplexml_load_string($xmlString);

        if (!$xmlSettings instanceof SimpleXMLElement) {
            throw new ConfigurationException('Error when loading configuration. Invalid XML: ' . $configFilename);
        }

        # load ENV variables
        $this->loadPHPEnvironment($xmlSettings);

        # load translation-sets
        $suites = $this->loadTranslations($xmlSettings, $configFilename);

        # create and validate
        # the configuration object
        $config = new Configuration($suites);
        $this->validateConfig($config);

        return $config;
    }

    /**
     * @param SimpleXMLElement $rootNode
     * @return void
     * @throws ConfigurationException
     */
    private function loadPHPEnvironment(SimpleXMLElement $rootNode): void
    {
        $phpNodeValues = $rootNode->php->children();

        if ($phpNodeValues === null) {
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
     * @param string $configFilename
     * @return TranslationSet[]
     * @throws \Exception
     */
    private function loadTranslations(SimpleXMLElement $rootNode, string $configFilename): array
    {
        $suites = [];

        /** @var SimpleXMLElement $xmlSet */
        foreach ($rootNode->translations->children() as $xmlSet) {

            $setName = trim((string)$xmlSet['name']);
            $nodeFormat = $xmlSet->format;
            $nodeLocales = $xmlSet->locales;
            $nodeFilter = $xmlSet->filter;
            $nodeStyles = $xmlSet->styles;


            # default values
            $setFormat = 'json';
            $setAttributes = [];
            $setLocales = [];
            $setFilter = new Filter();
            $casingStyles = [];

            if ($nodeFormat !== null) {
                $formatData = $this->parseFormat($nodeFormat);
                $setFormat = $formatData['format'];
                $setAttributes = $formatData['attributes'];
            }

            if ($nodeFilter !== null) {
                $setFilter = $this->loadFilter($nodeFilter);
            }

            if ($nodeLocales !== null) {
                $setLocales = $this->loadLocales($nodeLocales, $configFilename);
            }

            if ($nodeStyles !== null) {
                $casingStyles = $this->loadStyles($nodeStyles);
            }

            $set = new TranslationSet(
                $setName,
                $setFormat,
                $setLocales,
                $setFilter,
                $setAttributes,
                $casingStyles
            );

            $storage = StorageFactory::getStorage($set);


            # some storages do not support filtering
            # so make sure to throw an exception if we have a filter config.
            # this is because filters are also used in imports (at least affects imports)
            # which means that they would lead to removed keys on FILE-type storages.
            if (!$storage->supportsFilters()) {

                if ($set->getFilter()->hasFilters()) {
                    throw new ConfigurationException('Filters are not allowed for storage format: ' . $setFormat);
                }
            }


            # now iterate through our locales
            # and load the translation files for it
            $storage->loadTranslations($set);

            # remove fields that must not be existing
            # because of our allow or exclude list
            $this->filterHandler->applyFilter($set);

            $suites[] = $set;
        }

        return $suites;
    }


    /**
     * @param SimpleXMLElement $rootFormat
     * @return array<mixed>
     * @throws \Exception
     */
    private function parseFormat(SimpleXMLElement $rootFormat)
    {
        $children = get_object_vars($rootFormat);

        if (count($children) <= 0) {
            throw new \Exception('No format provided');
        }

        if (count($children) >= 2) {
            throw new \Exception('Only 1 format allowed');
        }

        foreach ($children as $formatTag => $formatElement) {
            $format = $formatTag;
            $setAttributes = $this->getAttributes($formatElement);

            return [
                'format' => $format,
                'attributes' => $setAttributes,
            ];
        }
    }

    /**
     * @param SimpleXMLElement $filterNode
     * @return Filter
     */
    private function loadFilter(SimpleXMLElement $filterNode): Filter
    {
        $filter = new Filter();

        $nodeAllows = $filterNode->include;
        $nodeExcludes = $filterNode->exclude;

        $nodeAllowsKeys = ($nodeAllows !== null) ? $nodeAllows->key : null;
        $nodeExcludeKeys = ($nodeExcludes !== null) ? $nodeExcludes->key : null;

        if ($nodeAllowsKeys !== null) {
            foreach ($nodeAllowsKeys as $key) {
                $filter->addIncludeKey((string)$key);
            }
        }

        if ($nodeExcludeKeys !== null) {
            foreach ($nodeExcludeKeys as $key) {
                $filter->addExcludeKey((string)$key);
            }
        }

        return $filter;
    }


    /**
     * @param SimpleXMLElement $stylesNode
     * @return array<mixed>
     */
    private function loadStyles(SimpleXMLElement $stylesNode): array
    {
        $styles = [];

        if ($stylesNode->style === null) {
            return [];
        }

        foreach ($stylesNode->style as $style) {

            $styleName = (string)$style;

            if (!in_array($styleName, $styles)) {
                $styles[] = $styleName;
            }
        }

        return $styles;
    }

    /**
     * @param SimpleXMLElement $rootLocales
     * @param string $configFilename
     * @return array<mixed>
     * @throws ConfigurationException
     */
    private function loadLocales(SimpleXMLElement $rootLocales, string $configFilename): array
    {
        $foundLocales = [];

        foreach ($rootLocales->children() as $nodeLocale) {

            $nodeType = $nodeLocale->getName();
            $innerValue = (string)$nodeLocale[0];

            if ($nodeType !== 'locale') {
                throw new ConfigurationException('only <locale> elements are allowed in the locales node. found: ' . $nodeType);
            }

            $localeName = (string)$nodeLocale['name'];
            $localeFile = '';
            $iniSection = (string)$nodeLocale['iniSection'];


            if (trim($localeName) === '') {
                throw new ConfigurationException('empty locale attributes are not allowed in set: ' . $configFilename);
            }

            if ($innerValue !== '') {
                # for now treat inner value as file
                $configuredFileName = dirname($configFilename) . '/' . $innerValue;
                $localeFile = realpath($configuredFileName);

                if ($localeFile === false || !file_exists($localeFile)) {
                    throw new ConfigurationException('Attention, translation file not found: ' . $configuredFileName);
                }
            }

            $foundLocales[] = new Locale($localeName, $localeFile, $iniSection);
        }

        return $foundLocales;
    }

    /**
     * @param Configuration $configuration
     * @return void
     * @throws \Exception
     */
    private function validateConfig(Configuration $configuration)
    {
        $foundSets = [];

        foreach ($configuration->getTranslationSets() as $set) {

            if ($set->getName() === '') {
                throw new ConfigurationException('TranslationSet has no name. This is required!');
            }

            if ($set->getFormat() === '') {
                throw new ConfigurationException('TranslationSet has no format. This is required!');
            }

            if (in_array($set->getName(), $foundSets)) {
                throw new ConfigurationException('TranslationSet "' . $set->getName() . '" has already been found');
            }

            $foundSets[] = $set->getName();


            $foundLocales = [];

            foreach ($set->getLocales() as $locale) {

                if ($locale->getName() === '') {
                    throw new ConfigurationException('Locale has no name. This is required!');
                }

                if (in_array($locale->getName(), $foundLocales)) {
                    throw new ConfigurationException('Locale "' . $locale->getName() . '" has already been found in Translation-Set: ' . $set->getName());
                }

                $foundLocales[] = $locale->getName();
            }
        }
    }

    /**
     * @param SimpleXMLElement $node
     * @return array<mixed>
     */
    private function getAttributes(SimpleXMLElement $node)
    {
        $setAttributes = [];
        $nodeAttributes = $node->attributes();
        if ($nodeAttributes !== null) {
            foreach ($nodeAttributes as $attrName => $value) {
                $setAttributes[] = new Attribute($attrName, $value);
            }
        }

        return $setAttributes;
    }
}
