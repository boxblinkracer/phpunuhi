<?php

declare(strict_types=1);

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

    private LocalesPlaceholderProcessor $placholderProcessor;



    public function __construct()
    {
        $this->placholderProcessor = new LocalesPlaceholderProcessor();
    }


    /**
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
            $isBase = isset($nodeLocale['base']) && $this->getBool((string)$nodeLocale['base']);

            $localeFile = $this->placholderProcessor->buildRealLocaleFilename(
                $localeName,
                $innerValue,
                $basePath->getValue(),
                $configFilename
            );

            $foundLocales[] = new Locale($localeName, $isBase, $localeFile, $iniSection);
        }

        # check if we have 2 base locales, and throw an exception if so
        $baseLocales = array_filter($foundLocales, function (Locale $locale): bool {
            return $locale->isBase();
        });

        if (count($baseLocales) > 1) {
            throw new ConfigurationException('Only 1 locale can be defined as the base locale within a translation-set');
        }

        return $foundLocales;
    }
}
