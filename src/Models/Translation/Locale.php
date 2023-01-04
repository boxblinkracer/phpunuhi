<?php

namespace PHPUnuhi\Models\Translation;

class Locale
{

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $filename;


    /**
     * @var Translation[]
     */
    private $translations;


    /**
     * @param string $locale
     * @param string $filename
     */
    public function __construct(string $locale, string $filename)
    {
        $this->locale = $locale;
        $this->filename = $filename;

        $this->translations = [];
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addTranslation(string $key, string $value): void
    {
        $this->translations[] = new Translation(
            $this->locale,
            $key,
            $value
        );
    }

    /**
     * @return Translation[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @return array<string>
     */
    public function getTranslationKeys(): array
    {
        $keys = [];

        foreach ($this->getTranslations() as $translation) {
            if (!in_array($translation->getKey(), $keys)) {
                $keys[] = $translation->getKey();
            }
        }

        return $keys;
    }

}

