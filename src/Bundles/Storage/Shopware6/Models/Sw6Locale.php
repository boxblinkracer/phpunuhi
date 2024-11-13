<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

class Sw6Locale
{
    private string $languageId;

    private string $languageName;

    private string $localeName;


    public function __construct(string $languageId, string $languageName, string $localeName)
    {
        $this->languageId = $languageId;
        $this->languageName = $languageName;
        $this->localeName = $localeName;
    }


    public function getLanguageId(): string
    {
        return $this->languageId;
    }


    public function getLanguageName(): string
    {
        return $this->languageName;
    }


    public function getLocaleName(): string
    {
        return $this->localeName;
    }
}
