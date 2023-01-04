<?php

namespace PHPUnuhi\Services\Configuration;

use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use SimpleXMLElement;

class ConfigurationLoader
{

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

            $suite = new TranslationSet($name, $foundLocales);

            $suite = $this->loadTranslations($suite);

            $suites[] = $suite;
        }

        return new Configuration($suites);
    }

    /**
     * @param TranslationSet $suite
     * @return TranslationSet
     */
    private function loadTranslations(TranslationSet $suite): TranslationSet
    {
        foreach ($suite->getLocales() as $locale) {

            $snippetJson = (string)file_get_contents($locale->getFilename());

            $foundTranslations = [];

            if (!empty($snippetJson)) {
                $foundTranslations = json_decode($snippetJson, true);

                if ($foundTranslations === false) {
                    $foundTranslations = [];
                }
            }

            $foundTranslationsFlat = $this->getFlatArray($foundTranslations);

            foreach ($foundTranslationsFlat as $key => $value) {
                $locale->addTranslation($key, $value);
            }
        }

        return $suite;
    }

    /**
     * @param array<mixed> $array
     * @param string $prefix
     * @return array<string>
     */
    private function getFlatArray(array $array, string $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->getFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }
}