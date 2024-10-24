<?php

namespace PHPUnuhi\Models\Translation;

use Exception;
use PHPUnuhi\Exceptions\TranslationNotFoundException;

class Locale
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isBase;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $iniSection;

    /**
     * @var array<string, Translation>
     */
    private $translations = [];

    /**
     * @var array<string, int>
     */
    private $lineNumbers = [];


    /**
     * @param string $name
     * @param bool $isMain
     * @param string $filename
     * @param string $iniSection
     */
    public function __construct(string $name, bool $isMain, string $filename, string $iniSection)
    {
        $this->name = $name;
        $this->isBase = $isMain;
        $this->filename = $filename;
        $this->iniSection = $iniSection;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isBase(): bool
    {
        return $this->isBase;
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
        $id = empty($this->name) ? basename($this->filename) : $this->name;

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

        $this->translations[$translation->getID()] = $translation;

        return $translation;
    }

    /**
     * @param array<string, Translation> $translations
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return array<string, Translation>
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
        return array_keys($this->translations);
    }

    /**
     * @param string $searchID
     * @throws TranslationNotFoundException
     * @return Translation
     */
    public function findTranslation(string $searchID): Translation
    {
        if (!isset($this->translations[$searchID])) {
            throw new TranslationNotFoundException('No existing translation found for ID: ' . $searchID);
        }

        return $this->translations[$searchID];
    }

    /**
     * @param string $searchID
     * @return null|Translation
     */
    public function findTranslationOrNull(string $searchID): ?Translation
    {
        if (!isset($this->translations[$searchID])) {
            return null;
        }

        return $this->translations[$searchID];
    }

    /**
     * @return Translation[]
     */
    public function getValidTranslations(): array
    {
        return array_filter(
            $this->translations,
            function ($translation): bool {
                return !$translation->isEmpty();
            }
        );
    }

    /**
     * @param string $id
     * @return void
     */
    public function removeTranslation(string $id): void
    {
        unset($this->translations[$id]);
    }

    /**
     * @param string $oldKey
     * @param string $newKey
     * @throws TranslationNotFoundException
     * @return void
     */
    public function updateTranslationKey(string $oldKey, string $newKey): void
    {
        try {
            # check if our new key already exists
            $this->findTranslation($newKey);

            throw new Exception('Cannot update translation key. The new key already exists: ' . $newKey);
        } catch (TranslationNotFoundException $exception) {
        }

        $oldExisting = $this->findTranslation($oldKey);

        $this->removeTranslation($oldKey);

        $this->addTranslation(
            $newKey,
            $oldExisting->getValue(),
            $oldExisting->getGroup()
        );
    }
}
