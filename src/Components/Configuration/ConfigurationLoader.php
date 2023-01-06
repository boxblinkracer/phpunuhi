<?php

namespace PHPUnuhi\Configuration;

use PHPUnuhi\Bundles\Translation\Format;
use PHPUnuhi\Bundles\Translation\JSON\JSONTranslationLoader;
use PHPUnuhi\Bundles\Translation\TranslationFactory;
use PHPUnuhi\Bundles\Translation\TranslationLoaderInterface;
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

        /** @var SimpleXMLElement $translation */
        foreach ($xmlSettings->translations->children() as $translation) {

            $name = (string)$translation['name'];
            $format = (string)$translation['format'];

            if (empty($format)) {
                $format = Format::JSON;
            }

            $translationLoader = TranslationFactory::getLoaderFromFormat($format);

            $foundLocales = [];

            /** @var SimpleXMLElement $childNode */
            foreach ($translation->children() as $childNode) {

                $nodeType = $childNode->getName();
                $nodeValue = (string)$childNode[0];

                $locale = null;

                switch ($nodeType) {
                    case 'file':
                        $localeAttr = (string)$childNode['locale'];
                        $fileName = (string)realpath(dirname($configFilename) . '/' . $nodeValue);

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
            $set = new TranslationSet($name, $format, $foundLocales);

            # now iterate through our locales
            # and load the translation files for it
            foreach ($set->getLocales() as $locale) {
                $translationLoader->loadTranslations($locale);
            }

            $suites[] = $set;
        }

        return new Configuration($suites);
    }

}