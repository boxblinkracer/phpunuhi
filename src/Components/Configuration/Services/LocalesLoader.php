<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Traits\BoolTrait;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class LocalesLoader
{
    use XmlTrait;
    use BoolTrait;

    /**
     * @var LocalesPlaceholderProcessor
     */
    private $placholderProcessor;


    /**
     *
     */
    public function __construct()
    {
        $this->placholderProcessor = new LocalesPlaceholderProcessor();
    }


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
                continue;
            }

            $localeName = (string)$nodeLocale['name'];
            $iniSection = (string)$nodeLocale['iniSection'];
            $isMain = isset($nodeLocale['base']) && $this->getBool((string)$nodeLocale['base']);

            $localeFile = $this->placholderProcessor->buildRealFilename(
                $localeName,
                $innerValue,
                $basePath->getValue(),
                $configFilename
            );

            $foundLocales[] = new Locale($localeName, $isMain, $localeFile, $iniSection);
        }

        return $foundLocales;
    }
}
