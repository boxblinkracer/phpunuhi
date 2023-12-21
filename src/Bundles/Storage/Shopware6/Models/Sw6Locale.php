<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

class Sw6Locale
{

    /**
     * @var string
     */
    private $languageId;

    /**
     * @var string
     */
    private $languageName;

    /**
     * @var string
     */
    private $localeName;

    /**
     * @param string $languageId
     * @param string $languageName
     * @param string $localeName
     */
    public function __construct(string $languageId, string $languageName, string $localeName)
    {
        $this->languageId = $languageId;
        $this->languageName = $languageName;
        $this->localeName = $localeName;
    }

    /**
     * @return string
     */
    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    /**
     * @return string
     */
    public function getLanguageName(): string
    {
        return $this->languageName;
    }

    /**
     * @return string
     */
    public function getLocaleName(): string
    {
        return $this->localeName;
    }
}
