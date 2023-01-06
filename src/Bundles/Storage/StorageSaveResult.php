<?php

namespace PHPUnuhi\Bundles\Storage;

class StorageSaveResult
{

    /**
     * @var int
     */
    private $savedLocales;

    /**
     * @var int
     */
    private $savedTranslations;

    /**
     * @param int $savedLocales
     * @param int $savedTranslations
     */
    public function __construct(int $savedLocales, int $savedTranslations)
    {
        $this->savedLocales = $savedLocales;
        $this->savedTranslations = $savedTranslations;
    }

    /**
     * @return int
     */
    public function getSavedLocales(): int
    {
        return $this->savedLocales;
    }

    /**
     * @return int
     */
    public function getSavedTranslations(): int
    {
        return $this->savedTranslations;
    }

}
