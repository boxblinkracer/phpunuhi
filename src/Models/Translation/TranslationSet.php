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
     * @var Locale[]
     */
    private $locales;


    /**
     * @param string $name
     * @param string $format
     * @param int $jsonIndent
     * @param bool $sortStorage
     * @param Locale[] $locales
     */
    public function __construct(string $name, string $format, int $jsonIndent, bool $sortStorage, array $locales)
    {
        $this->name = $name;
        $this->format = $format;
        $this->jsonIndent = $jsonIndent;
        $this->sortStorage = $sortStorage;
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
     * @return int
     */
    public function getJsonIndent(): int
    {
        return $this->jsonIndent;
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
