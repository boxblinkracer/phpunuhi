<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Translator\DeepL\Services;

use DeepL;
use DeepL\DeepLException;
use DeepL\Translator;

class SupportedLanguages
{
    private Translator $translator;

    /**
     * @var array<DeepL\Language>
     */
    private static $supportedLanguages;

    private const must_have_country = [
        'en' => 'en-gb',
        'pt' => 'pt-pt'
    ];

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @throws DeepLException
     */
    public function getAvailableLocale(string $locale): string
    {
        $locale = strtolower($locale);

        if (isset(self::must_have_country[$locale])) {
            return self::must_have_country[$locale];
        }

        $supportedLocales = $this->getAvailableLanguages();

        if (preg_match("/^([a-z]{2,})-[a-z]{2,}$/", $locale, $matches)) {
            $localeCountry = $locale;
            $locale = $matches[1];
        }

        if (isset($localeCountry) && isset($supportedLocales[$localeCountry])) {
            return $localeCountry;
        }

        if (isset($supportedLocales[$locale])) {
            return $locale;
        }

        throw new DeepLException(
            'Supported languages are: ' . implode(', ', $supportedLocales) .
            ' Not `' . $locale . '`'
        );
    }

    /**
     * @return array<DeepL\Language>
     */
    private function getAvailableLanguages(): array
    {
        if (self::$supportedLanguages !== null) {
            return self::$supportedLanguages;
        }

        self::$supportedLanguages = [];

        /** @var DeepL\Language $language */
        foreach ($this->translator->getTargetLanguages() as $language) {
            $locale = strtolower($language->code);
            self::$supportedLanguages[$locale] = $language;
        }

        return self::$supportedLanguages;
    }
}
