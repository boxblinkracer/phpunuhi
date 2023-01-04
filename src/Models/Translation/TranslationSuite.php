<?php

namespace PHPUnuhi\Models\Translation;

class TranslationSuite
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Locale[]
     */
    private $locales;


    /**
     * @param string $name
     * @param Locale[] $locales
     */
    public function __construct(string $name, array $locales)
    {
        $this->name = $name;
        $this->locales = $locales;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return array<mixed>
     */
    public function getAllTranslationKeys(): array
    {
        $allKeys = [];

        foreach ($this->locales as $locale) {
            foreach ($locale->getTranslationKeys() as $key) {
                if (!in_array($key, $allKeys)) {
                    $allKeys[] = $key;
                }
            }
        }
        return $allKeys;
    }

}
