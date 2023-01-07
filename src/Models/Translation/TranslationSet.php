<?php

namespace PHPUnuhi\Models\Translation;

use _PHPStan_d279f388f\Nette\Neon\Exception;

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
    private $jsonIntent;

    /**
     * @var bool
     */
    private $jsonSort;

    /**
     * @var Locale[]
     */
    private $locales;


    /**
     * @param string $name
     * @param string $format
     * @param int $jsonIntent
     * @param bool $jsonSort
     * @param Locale[] $locales
     */
    public function __construct(string $name, string $format, int $jsonIntent, bool $jsonSort, array $locales)
    {
        $this->name = $name;
        $this->format = $format;
        $this->jsonIntent = $jsonIntent;
        $this->jsonSort = $jsonSort;
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
    public function getJsonIntent(): int
    {
        return $this->jsonIntent;
    }

    /**
     * @return bool
     */
    public function isJsonSort(): bool
    {
        return $this->jsonSort;
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
