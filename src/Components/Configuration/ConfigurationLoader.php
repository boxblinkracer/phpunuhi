<?php

namespace PHPUnuhi\Configuration;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageFormat;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Translation\Filter;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use SimpleXMLElement;


class ConfigurationLoader
{

    /**
     *
     */
    public function __construct()
    {
    }


    /**
     * @param string $configFilename
     * @return Configuration
     * @throws \Exception
     */
    public function load(string $configFilename)
    {
        $xmlString = (string)file_get_contents($configFilename);
        $xmlSettings = simplexml_load_string($xmlString);

        if (!$xmlSettings instanceof SimpleXMLElement) {
            throw new \Exception('Error when loading configuration. Invalid XML: ' . $configFilename);
        }


        $suites = [];

        $phpNodeValues = $xmlSettings->php->children();
        if ($phpNodeValues !== null) {
            foreach ($phpNodeValues as $xmlSet) {

                $name = trim((string)$xmlSet['name']);
                $value = trim((string)$xmlSet['value']);

                $_SERVER[$name] = $value;
            }
        }


        /** @var SimpleXMLElement $xmlSet */
        foreach ($xmlSettings->translations->children() as $xmlSet) {

            $name = trim((string)$xmlSet['name']);
            $format = trim((string)$xmlSet['format']);
            $jsonIndent = trim((string)$xmlSet['jsonIndent']);
            $sw6Entity = trim((string)$xmlSet['sw6Entity']);
            $sortStorage = trim((string)$xmlSet['sort']);

            if (empty($format)) {
                $format = StorageFormat::JSON;
            }

            if (empty($jsonIndent)) {
                $jsonIndent = "2";
            }

            if (empty($sortStorage)) {
                $sortStorage = "false";
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

                    case 'file':
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
                (int)$jsonIndent,
                (bool)$sortStorage,
                $sw6Entity,
                $foundLocales,
                $filter
            );

            $translationLoader = StorageFactory::getStorage($set->getFormat(), $set->getJsonIndent(), $set->isSortStorage());

            # now iterate through our locales
            # and load the translation files for it
            $translationLoader->loadTranslations($set);

            $suites[] = $set;
        }

        $config = new Configuration($suites);

        $this->validateConfig($config);

        return $config;
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