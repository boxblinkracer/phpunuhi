<?php

namespace PHPUnuhi\Models\Translation;

use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\CaseStyle;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;

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
     * @var Protection
     */
    private $protection;

    /**
     * @var Attribute[]
     */
    private $attributes;

    /**
     * @var Locale[]
     */
    private $locales;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CaseStyle[]
     */
    private $casingStyles;

    /**
     * @var Rule[]
     */
    private $rules;


    /**
     * @param string $name
     * @param string $format
     * @param Protection $protection
     * @param Locale[] $locales
     * @param Filter $filter
     * @param Attribute[] $attributes
     * @param CaseStyle[] $styles
     * @param Rule[] $rules
     */
    public function __construct(string $name, string $format, Protection $protection, array $locales, Filter $filter, array $attributes, array $styles, array $rules)
    {
        $this->name = $name;
        $this->format = $format;
        $this->protection = $protection;
        $this->locales = $locales;
        $this->filter = $filter;
        $this->attributes = $attributes;
        $this->casingStyles = $styles;
        $this->rules = $rules;
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
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return Protection
     */
    public function getProtection(): Protection
    {
        return $this->protection;
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAttributeValue(string $name): string
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute->getValue();
            }
        }

        return '';
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return CaseStyle[]
     */
    public function getCasingStyles(): array
    {
        return $this->casingStyles;
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
    public function getAllTranslationIDs(): array
    {
        $allIDs = [];

        foreach ($this->locales as $locale) {
            foreach ($locale->getTranslationIDs() as $key) {
                if (!in_array($key, $allIDs)) {
                    $allIDs[] = $key;
                }
            }
        }
        return $allIDs;
    }

    /**
     * @param string $searchID
     * @param string $searchLocale
     * @throws TranslationNotFoundException
     * @return array<mixed>
     */
    public function findAnyExistingTranslation(string $searchID, string $searchLocale): array
    {
        foreach ($this->locales as $currentLocale) {

            # if we have a search locale set,
            # then only search for translations of this locale.
            # if it does not match, continue
            if ($searchLocale !== '' && $searchLocale !== '0' && $currentLocale->getName() !== $searchLocale) {
                continue;
            }

            foreach ($currentLocale->getTranslations() as $translation) {
                if ($translation->getID() !== $searchID) {
                    continue;
                }
                if ($translation->isEmpty()) {
                    continue;
                }
                # should be an object, just too lazy atm
                return [
                    'locale' => $currentLocale->getName(),
                    'translation' => $translation,
                ];
            }
        }

        throw new TranslationNotFoundException('No valid translation found for ID: ' . $searchID);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function isCompletelyTranslated(string $id): bool
    {
        $complete = true;

        foreach ($this->getLocales() as $locale) {
            try {
                $trans = $locale->findTranslation($id);

                if ($trans->isEmpty()) {
                    $complete = false;
                    break;
                }
            } catch (TranslationNotFoundException $ex) {
                $complete = false;
                break;
            }
        }

        return $complete;
    }

    /**
     * @param int $getLevel
     * @return string
     */
    public function getCasingStyle(int $getLevel): string
    {
        foreach ($this->casingStyles as $style) {
            if ($style->getLevel() === $getLevel) {
                return $style->getName();
            }
        }

        foreach ($this->casingStyles as $style) {
            if ($style->hasLevel() === false) {
                return $style->getName();
            }
        }

        return '';
    }

    /**
     * @throws TranslationNotFoundException
     * @return array<mixed>
     */
    public function getInvalidTranslationsIDs(): array
    {
        $invalidTranslations = [];

        foreach ($this->getAllTranslationIDs() as $id) {
            $hasValueInAnyLocale = false;

            foreach ($this->locales as $locale) {
                $translation = $locale->findTranslation($id);

                if (!$translation->isEmpty()) {
                    $hasValueInAnyLocale = true;
                    break;
                }
            }

            if (!$hasValueInAnyLocale) {
                $invalidTranslations[] = $id;
            }
        }

        return $invalidTranslations;
    }
}
