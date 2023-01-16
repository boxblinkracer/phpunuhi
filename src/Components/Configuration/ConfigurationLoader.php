<?php

namespace PHPUnuhi\Configuration;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Translation\Attribute;
use PHPUnuhi\Models\Translation\Filter;
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
            throw new \Exception('Error when loading configuration. Invalid XML: ' . $configFilename);
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

            $name = trim((string)$xmlSet['name']);
            $format = trim((string)$xmlSet['format']);

            $setAttributes = [];
            $nodeAttributes = $xmlSet->attributes();
            if ($nodeAttributes !== null) {
                foreach ($nodeAttributes as $attrName => $value) {
                    $setAttributes[] = new Attribute($attrName, $value);
                }
            }


            $foundLocales = [];

            $filter = new Filter();

            /** @var SimpleXMLElement $childNode */
            foreach ($xmlSet->children() as $childNode) {

                $nodeType = $childNode->getName();
                $nodeValue = (string)$childNode[0];


                $locale = null;

                switch ($nodeType) {

                    case 'filter':
                        $filter = $this->loadFilter($childNode);
                        break;

                    case 'locale':

                        $configuredFileName = dirname($configFilename) . '/' . $nodeValue;
                        $fileName = realpath($configuredFileName);

                        if ($fileName === false || !file_exists($fileName)) {
                            throw new \Exception('Attention, translation file not found: ' . $configuredFileName);
                        }

                        $localeAttr = (string)$childNode['locale'];
                        $iniSection = (string)$childNode['iniSection'];

                        if (trim($localeAttr) === '') {
                            throw new \Exception('empty locale values are not allowed in set: ' . $configFilename);
                        }

                        $locale = new Locale($localeAttr, $fileName, $iniSection);
                        break;

                    case 'file':
                        throw new ConfigurationException('Children from type "file" are not possible anymore. Please use <locale>');

                    default:
                        throw new \Exception('child element not recognized in translation set: ' . $name);
                }

                if ($locale instanceof Locale) {
                    $foundLocales[] = $locale;
                }
            }

            # create our new set
            $set = new TranslationSet(
                $name,
                $format,
                $foundLocales,
                $filter,
                $setAttributes
            );

            $translationLoader = StorageFactory::getStorage($set);

            # now iterate through our locales
            # and load the translation files for it
            $translationLoader->loadTranslations($set);

            # remove fields that must not be existing
            # because of our allow or exclude list
            $this->filterHandler->applyFilter($set);

            $suites[] = $set;
        }

        return $suites;
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

        $nodeAllowsKeys = $nodeAllows->key;
        $nodeExcludeKeys = $nodeExcludes->key;

        if ($nodeAllowsKeys !== null) {
            foreach ($nodeAllowsKeys as $key) {
                $filter->addAllowKey((string)$key);
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
     * @param Configuration $configuration
     * @return void
     * @throws \Exception
     */
    private function validateConfig(Configuration $configuration)
    {
        $foundSets = [];

        foreach ($configuration->getTranslationSets() as $set) {

            if ($set->getName() === '') {
                throw new \Exception('TranslationSet has no name. This is required!');
            }

            if ($set->getFormat() === '') {
                throw new \Exception('TranslationSet has no format. This is required!');
            }

            if (in_array($set->getName(), $foundSets)) {
                throw new \Exception('TranslationSet "' . $set->getName() . '" has already been found');
            }

            $foundSets[] = $set->getName();


            $foundLocales = [];

            foreach ($set->getLocales() as $locale) {

                if ($locale->getName() === '') {
                    throw new \Exception('Locale has no name. This is required!');
                }

                if ($locale->getFilename() === '') {
                    throw new \Exception('Locale has no filename. This is required!');
                }

                if (in_array($locale->getName(), $foundLocales)) {
                    throw new \Exception('Locale "' . $locale->getName() . '" has already been found in Translation-Set: ' . $set->getName());
                }

                $foundLocales[] = $locale->getName();
            }
        }
    }

}
