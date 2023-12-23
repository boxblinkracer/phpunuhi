<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class LocalesLoader
{
    use XmlTrait;

    /**
     * @param SimpleXMLElement $rootLocales
     * @param string $configFilename
     * @throws ConfigurationException
     * @return array<mixed>
     */
    public function loadLocales(SimpleXMLElement $rootLocales, string $configFilename): array
    {
        $foundLocales = [];

        # load optional <locale basePath=xy">
        $basePath = $this->getAttribute('basePath', $rootLocales);

        foreach ($rootLocales->children() as $nodeLocale) {
            $nodeType = $nodeLocale->getName();
            $innerValue = trim((string)$nodeLocale[0]);

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

                # replace our locale-name placeholders
                $innerValue = str_replace('%locale%', $localeName, $innerValue);
                $innerValue = str_replace('%locale_uc%', strtoupper($localeName), $innerValue);
                $innerValue = str_replace('%locale_lc%', strtolower($localeName), $innerValue);

                # if we have a basePath, we also need to replace any values
                if ($basePath->getValue() !== '' && $basePath->getValue() !== '0') {
                    $innerValue = str_replace('%base_path%', $basePath->getValue(), $innerValue);
                }

                # for now treat inner value as file
                $configuredFileName = dirname($configFilename) . '/' . $innerValue;

                $localeFile = (string)realpath($configuredFileName);

                if (!file_exists($localeFile)) {
                    throw new ConfigurationException('Attention, translation file not found: ' . $configuredFileName);
                }
            }

            $foundLocales[] = new Locale($localeName, $localeFile, $iniSection);
        }

        return $foundLocales;
    }
}
