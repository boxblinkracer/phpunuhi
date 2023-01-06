<?php

namespace PHPUnuhi\Models\Translation;

class TranslationSet
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $format;

    /**
     * @var Locale[]
     */
    private $locales;


    /**
     * @param string $name
     * @param string $format
     * @param Locale[] $locales
     */
    public function __construct(string $name, string $format, array $locales)
    {
        $this->name = $name;
        $this->format = $format;
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
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
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
