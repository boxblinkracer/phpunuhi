<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Translation;

class LazyLocale extends Locale
{
    private Locale $locale;

    private LazyTranslationSet $lazyTranslationSet;

    public function __construct(
        Locale $locale,
        LazyTranslationSet $lazyTranslationSet
    ) {
        parent::__construct('', false, '', '');

        $this->locale = $locale;
        $this->lazyTranslationSet = $lazyTranslationSet;
    }

    public function getTranslations(): array
    {
        $this->lazyTranslationSet->loadFromStorage();
        return $this->locale->getTranslations();
    }

    public function getTranslationIDs(): array
    {
        $this->lazyTranslationSet->loadFromStorage();
        return $this->locale->getTranslationIDs();
    }

    public function getValidTranslations(): array
    {
        $this->lazyTranslationSet->loadFromStorage();
        return $this->locale->getValidTranslations();
    }

    public function getName(): string
    {
        return $this->locale->getName();
    }

    public function isBase(): bool
    {
        return $this->locale->isBase();
    }

    public function getFilename(): string
    {
        return $this->locale->getFilename();
    }

    public function getExchangeIdentifier(): string
    {
        return $this->locale->getExchangeIdentifier();
    }

    public function getIniSection(): string
    {
        return $this->locale->getIniSection();
    }

    public function addTranslation(string $key, string $value, string $group): Translation
    {
        return $this->locale->addTranslation($key, $value, $group);
    }

    public function setTranslations(array $translations): void
    {
        $this->locale->setTranslations($translations);
    }

    public function setLineNumbers(array $lineNumbers): void
    {
        $this->locale->setLineNumbers($lineNumbers);
    }

    public function getLineNumbers(): array
    {
        return $this->locale->getLineNumbers();
    }

    public function findLineNumber(string $key): int
    {
        return $this->locale->findLineNumber($key);
    }

    public function findTranslation(string $searchID): Translation
    {
        return $this->locale->findTranslation($searchID);
    }

    public function findTranslationOrNull(string $searchID): ?Translation
    {
        return $this->locale->findTranslationOrNull($searchID);
    }

    public function removeTranslation(string $id): void
    {
        $this->locale->removeTranslation($id);
    }

    public function updateTranslationKey(string $oldKey, string $newKey): void
    {
        $this->locale->updateTranslationKey($oldKey, $newKey);
    }
}
