<?php

namespace Configuration;


use PHPUnuhi\Bundles\JSON\TranslationLoader;
use PHPUnuhi\Bundles\TranslationLoaderInterface;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use SimpleXMLElement;


class ConfigurationLoader
{

    /**
     * @var TranslationLoaderInterface
     */
    private $translationLoader;


    /**
     * @param string $format
     * @return ConfigurationLoader
     * @throws \Exception
     */
    public static function fromFormat(string $format): ConfigurationLoader
    {
        switch (strtolower($format)) {
            case 'json':
                return new ConfigurationLoader(new TranslationLoader());

            default:
                throw new \Exception('Unknown format: ' . $format);
        }
    }

    /**
     * @param TranslationLoaderInterface $translationLoader
     */
    private function __construct(TranslationLoaderInterface $translationLoader)
    {
        $this->translationLoader = $translationLoader;
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
                }

                if ($locale instanceof Locale) {
                    $foundLocales[] = $locale;
                }
            }

            # create our new set
            $set = new TranslationSet($name, $foundLocales);

            # now iterate through our locales
            # and load the translation files for it
            foreach ($set->getLocales() as $locale) {
                $this->translationLoader->loadTranslations($locale);
            }

            $suites[] = $set;
        }

        return new Configuration($suites);
    }

}