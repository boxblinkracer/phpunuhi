<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Translation;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;

class LazyTranslationSet extends TranslationSet
{
    private ?StorageInterface $storage = null;

    private ?FilterHandler $filterHandler = null;

    private bool $initialized = false;

    /**
     * @param Locale[] $locales
     * @param Attribute[] $attributes
     * @param Rule[] $rules
     */
    public function __construct(string $name, string $format, Protection $protection, array $locales, Filter $filter, array $attributes, CaseStyleSetting $styles, array $rules)
    {
        $lazyLocales = array_map(
            function ($locale): LazyLocale {
                return new LazyLocale($locale, $this);
            },
            $locales
        );

        parent::__construct($name, $format, $protection, $lazyLocales, $filter, $attributes, $styles, $rules);
    }

    public function getLocales(): array
    {
        return parent::getLocales();
    }

    public function getAllTranslationIDs(): array
    {
        $this->loadFromStorage();
        return parent::getAllTranslationIDs();
    }

    public function findAnyExistingTranslation(string $searchID, string $sourceLocaleName): array
    {
        $this->loadFromStorage();
        return parent::findAnyExistingTranslation($searchID, $sourceLocaleName);
    }

    public function isCompletelyTranslated(string $id): bool
    {
        $this->loadFromStorage();
        return parent::isCompletelyTranslated($id);
    }

    public function getInvalidTranslationsIDs(): array
    {
        $this->loadFromStorage();
        return parent::getInvalidTranslationsIDs();
    }

    public function setStorage(StorageInterface $storage, ?FilterHandler $filterHandler): self
    {
        $this->storage = $storage;
        $this->filterHandler = $filterHandler;
        return $this;
    }

    public function loadFromStorage(): void
    {
        if ($this->initialized || !$this->storage instanceof StorageInterface || !$this->filterHandler instanceof FilterHandler) {
            return;
        }

        $this->initialized = true;

        $this->storage->loadTranslationSet($this);

        if ($this->filterHandler instanceof FilterHandler) {
            # remove fields that must not be existing
            # because of our allow or exclude list
            $this->filterHandler->applyFilter($this);
        }
    }
}
