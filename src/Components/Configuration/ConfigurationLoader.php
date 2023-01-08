<?php

namespace PHPUnuhi\Configuration;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageFormat;
use PHPUnuhi\Models\Configuration\Configuration;
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

        /** @var SimpleXMLElement $xmlSet */
        foreach ($xmlSettings->translations->children() as $xmlSet) {

            $name = trim((string)$xmlSet['name']);
            $format = trim((string)$xmlSet['format']);
            $jsonIntent = trim((string)$xmlSet['jsonIntent']);
            $jsonSort = trim((string)$xmlSet['jsonSort']);

            if (empty($format)) {
                $format = StorageFormat::JSON;
            }

            if (empty($jsonIntent)) {
                $jsonIntent = "2";
            }

            if (empty($jsonSort)) {
                $jsonSort = "false";
            }

            $foundLocales = [];

            /** @var SimpleXMLElement $childNode */
            foreach ($xmlSet->children() as $childNode) {

                $nodeType = $childNode->getName();
                $nodeValue = (string)$childNode[0];

                $locale = null;

                switch ($nodeType) {
                    case 'file':

                        $fileName = (string)realpath(dirname($configFilename) . '/' . $nodeValue);
                        $localeAttr = (string)$childNode['locale'];

                        if (trim($localeAttr) === '') {
                            throw new \Exception('empty locale values are not allowed in set: ' . $configFilename);
                        }

                        $locale = new Locale($localeAttr, $fileName);
                        break;

                    default:
                        throw new \Exception('child element not recognized in translation set: ' . $name);
                }

                if ($locale instanceof Locale) {
                    $foundLocales[] = $locale;
                }
            }

            # create our new set
            $set = new TranslationSet($name, $format, (int)$jsonIntent, (bool)$jsonSort, $foundLocales);


            $translationLoader = StorageFactory::getStorage($set->getFormat(), $set->getJsonIntent(), $set->isJsonSort());

            # now iterate through our locales
            # and load the translation files for it
            foreach ($set->getLocales() as $locale) {
                $translationLoader->loadTranslations($locale);
            }

            $suites[] = $set;
        }

        $config = new Configuration($suites);

        $this->validateConfig($config);

        return $config;
    }


    /**
     * @param Configuration $configuration
     * @return void
     * @throws \Exception
     */
    private function validateConfig(Configuration $configuration)
    {
        foreach ($configuration->getTranslationSets() as $set) {

            if ($set->getName() === '') {
                throw new \Exception('TranslationSet has no name. This is required!');
            }

            if ($set->getFormat() === '') {
                throw new \Exception('TranslationSet has no format. This is required!');
            }

            foreach ($set->getLocales() as $locale) {

                if ($locale->getName() === '') {
                    throw new \Exception('Locale has no name. This is required!');
                }

                if ($locale->getFilename() === '') {
                    throw new \Exception('Locale has no filename. This is required!');
                }
            }
        }
    }

}