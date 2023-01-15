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
     * @var int
     */
    private $jsonIndent;

    /**
     * @var bool
     */
    private $sortStorage;

    /**
     * @var string
     */
    private $sw6Entity;

    /**
     * @var Locale[]
     */
    private $locales;

    /**
     * @var Filter
     */
    private $filter;


    /**
     * @param string $name
     * @param string $format
     * @param int $jsonIndent
     * @param bool $sortStorage
     * @param string $sw6Entity
     * @param Locale[] $locales
     * @param Filter $filter
     */
    public function __construct(string $name, string $format, int $jsonIndent, bool $sortStorage, string $sw6Entity, array $locales, Filter $filter)
    {
        $this->name = $name;
        $this->format = $format;
        $this->jsonIndent = $jsonIndent;
        $this->sortStorage = $sortStorage;
        $this->sw6Entity = $sw6Entity;
        $this->locales = $locales;

        $this->filter = $filter;
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
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return int
     */
    public function getJsonIndent(): int
    {
        return $this->jsonIndent;
    }

    /**
     * @return string
     */
    public function getSw6Entity(): string
    {
        return $this->sw6Entity;
    }

    /**
     * @return bool
     */
    public function isSortStorage(): bool
    {
        return $this->sortStorage;
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return bool
     */
    public function hasGroups(): bool
    {
        foreach ($this->locales as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                if (!empty($translation->getGroup())) {
                    return true;
                }
            }
        }

        return false;
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

    /**
     * @param string $searchKey
     * @return array<mixed>
     * @throws \Exception
     */
    public function findAnyExistingTranslation(string $searchKey): array
    {
        foreach ($this->locales as $locale) {

            foreach ($locale->getTranslations() as $translation) {

                if ($translation->getKey() === $searchKey && !$translation->isEmpty()) {
                    # should be an object, just too lazy atm
                    return [
                        'locale' => $locale->getName(),
                        'translation' => $translation,
                    ];
                }
            }
        }

        throw new \Exception('No valid translation found for key: ' . $searchKey);
    }

}
