<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Translation;

use Exception;
use PHPUnuhi\Exceptions\TranslationNotFoundException;

class Locale
{
    private string $name;

    private bool $isBase;

    private string $filename;

    private string $iniSection;

    /**
     * @var array<string, Translation>
     */
    private array $translations = [];

    /**
     * @var array<string, int>
     */
    private array $lineNumbers = [];



    public function __construct(string $name, bool $isMain, string $filename, string $iniSection)
    {
        $this->name = $name;
        $this->isBase = $isMain;
        $this->filename = $filename;
        $this->iniSection = $iniSection;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function isBase(): bool
    {
        return $this->isBase;
    }


    public function getFilename(): string
    {
        return $this->filename;
    }


    public function getExchangeIdentifier(): string
    {
        $id = $this->name === '' || $this->name === '0' ? basename($this->filename) : $this->name;

        # we use this also in technical environments
        # such as class and id names in HTML
        # so we need to remove spaces
        return str_replace(' ', '-', $id);
    }


    public function getIniSection(): string
    {
        return $this->iniSection;
    }


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
     * @throws TranslationNotFoundException
     */
    public function findTranslation(string $searchID): Translation
    {
        if (!isset($this->translations[$searchID])) {
            throw new TranslationNotFoundException('No existing translation found for ID: ' . $searchID);
        }

        return $this->translations[$searchID];
    }


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


    public function removeTranslation(string $id): void
    {
        unset($this->translations[$id]);
    }

    /**
     * @throws TranslationNotFoundException
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
