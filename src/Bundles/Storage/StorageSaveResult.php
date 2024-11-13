<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage;

class StorageSaveResult
{
    private int $savedLocales;

    private int $savedTranslations;



    public function __construct(int $savedLocales, int $savedTranslations)
    {
        $this->savedLocales = $savedLocales;
        $this->savedTranslations = $savedTranslations;
    }


    public function getSavedLocales(): int
    {
        return $this->savedLocales;
    }


    public function getSavedTranslations(): int
    {
        return $this->savedTranslations;
    }
}
