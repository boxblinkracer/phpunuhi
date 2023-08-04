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
     * @var array<string, int>
     */
    private $lineNumbers;


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
        $this->lineNumbers = [];
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
        if (!empty($this->name)) {
            $id = $this->name;
        } else {
            $id = basename($this->filename);
        }

        # we use this also in technical environments
        # such as class and id names in HTML
        # so we need to remove spaces
        return str_replace(' ', '-', $id);
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
     * @param string $group
     * @return Translation
     */
    public function addTranslation(string $key, string $value, string $group): Translation
    {
        $translation = new Translation(
            $key,
            $value,
            $group
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
     * @param array<string, int> $lineNumbers
     *
     * @return void
     */
    public function setLineNumbers(array $lineNumbers): void
    {
        $this->lineNumbers = $lineNumbers;
    }

    /**
     * @return array<string, int>
     */
    public function getLineNumbers(): array
    {
        return $this->lineNumbers;
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function findLineNumber(string $key): int
    {
        if (isset($this->lineNumbers[$key])) {
            return $this->lineNumbers[$key];
        }

        return 0;
    }

    /**
     * @return array<string>
     */
    public function getTranslationIDs(): array
    {
        $ids = [];

        foreach ($this->getTranslations() as $translation) {
            if (!in_array($translation->getID(), $ids)) {
                $ids[] = $translation->getID();
            }
        }

        return $ids;
    }

    /**
     * @param string $searchID
     * @return Translation
     * @throws TranslationNotFoundException
     */
    public function findTranslation(string $searchID): Translation
    {
        foreach ($this->getTranslations() as $translation) {

            if ($translation->getID() === $searchID) {
                return $translation;
            }
        }

        throw new TranslationNotFoundException('No existing translation found for ID: ' . $searchID);
    }

    /**
     * @return Translation[]
     */
    public function getValidTranslations(): array
    {
        $list = [];

        foreach ($this->getTranslations() as $translation) {
            if (!$translation->isEmpty()) {
                $list[] = $translation;
            }
        }

        return $list;
    }

    /**
     * @param string $id
     * @return void
     */
    public function removeTranslation(string $id): void
    {
        $tmpList = [];

        foreach ($this->translations as $translation) {

            if ($translation->getID() !== $id) {
                $tmpList[] = $translation;
            }
        }

        $this->translations = $tmpList;
    }
}

