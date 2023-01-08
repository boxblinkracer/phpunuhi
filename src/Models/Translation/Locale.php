<?php

namespace PHPUnuhi\Models\Translation;

use PHPUnuhi\Exceptions\TranslationNotFoundException;

class Locale
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $iniSection;

    /**
     * @var Translation[]
     */
    private $translations;


    /**
     * @param string $name
     * @param string $filename
     * @param string $iniSection
     */
    public function __construct(string $name, string $filename, string $iniSection)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->iniSection = $iniSection;

        $this->translations = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getExchangeIdentifier(): string
    {
        $id = "";

        if (!empty($this->name)) {
            $id = $this->name;
        } else {
            $id = basename($this->filename);
        }

        # we use this also in technical environments
        # such as class and id names in HTML
        # so we need to remove spaces
        $id = str_replace(' ', '-', $id);

        return $id;
    }

    /**
     * @return string
     */
    public function getIniSection(): string
    {
        return $this->iniSection;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Translation
     */
    public function addTranslation(string $key, string $value): Translation
    {
        $translation = new Translation(
            $key,
            $value
        );

        $this->translations[] = $translation;

        return $translation;
    }

    /**
     * @param array|Translation[] $translations
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
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

    /**
     * @param string $searchKey
     * @return Translation
     * @throws TranslationNotFoundException
     */
    public function findTranslation(string $searchKey): Translation
    {
        foreach ($this->getTranslations() as $translation) {

            if ($translation->getKey() === $searchKey) {
                return $translation;
            }
        }

        throw new TranslationNotFoundException('No existing translation found for key: ' . $searchKey);
    }

    /**
     * @return Translation[]
     */
    public function findEmptyTranslations(): array
    {
        $list = [];

        foreach ($this->getTranslations() as $translation) {
            if ($translation->isEmpty()) {
                $list[] = $translation;
            }
        }

        return $list;
    }

    /**
     * @return Translation[]
     */
    public function findFilledTranslations(): array
    {
        $list = [];

        foreach ($this->getTranslations() as $translation) {
            if (!$translation->isEmpty()) {
                $list[] = $translation;
            }
        }

        return $list;
    }
}

