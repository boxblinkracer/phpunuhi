<?php

namespace PHPUnuhi\Bundles\Exchange;

class ImportResult
{

    /**
     * @var int
     */
    private $countLocales;

    /**
     * @var int
     */
    private $countTranslations;

    /**
     * @param int $countLocales
     * @param int $countTranslations
     */
    public function __construct(int $countLocales, int $countTranslations)
    {
        $this->countLocales = $countLocales;
        $this->countTranslations = $countTranslations;
    }

    /**
     * @return int
     */
    public function getCountLocales(): int
    {
        return $this->countLocales;
    }

    /**
     * @return int
     */
    public function getCountTranslations(): int
    {
        return $this->countTranslations;
    }

}
