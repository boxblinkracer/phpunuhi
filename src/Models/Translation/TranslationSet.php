<?php

namespace PHPUnuhi\Models\Translation;

use Exception;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
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
     * @var CaseStyleSetting
     */
    private $casingStyleSettings;

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
     * @param CaseStyleSetting $styles
     * @param Rule[] $rules
     */
    public function __construct(string $name, string $format, Protection $protection, array $locales, Filter $filter, array $attributes, CaseStyleSetting $styles, array $rules)
    {
        $this->name = $name;
        $this->format = $format;
        $this->protection = $protection;
        $this->locales = $locales;
        $this->filter = $filter;
        $this->attributes = $attributes;
        $this->casingStyleSettings = $styles;
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
     * @param string $name
     * @return bool
     */
    public function hasRule(string $name): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param string $name
     * @throws Exception
     * @return Rule
     */
    public function getRule(string $name): Rule
    {
        foreach ($this->rules as $rule) {
            if ($rule->getName() === $name) {
                return $rule;
            }
        }

        throw new Exception('Rule not found: ' . $name);
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @return CaseStyleSetting
     */
    public function getCasingStyleSettings(): CaseStyleSetting
    {
        return $this->casingStyleSettings;
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
     * @param string $sourceLocaleName
     * @throws TranslationNotFoundException
     * @return array{locale: string, translation: Translation}
     */
    public function findAnyExistingTranslation(string $searchID, string $sourceLocaleName): array
    {
        foreach ($this->locales as $currentLocale) {
            if ($currentLocale->isBase()) {
                $sourceLocaleName = $currentLocale->getName();
                break;
            }
        }

        foreach ($this->locales as $currentLocale) {

            # if we have a source locale try to figure out if we are in this one
            # if not, then just continue with the next one
            if ($sourceLocaleName !== '' && $currentLocale->getName() !== $sourceLocaleName) {
                continue;
            }

            $translation = $currentLocale->findTranslationOrNull($searchID);
            if (!$translation) {
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
        $caseStyles = $this->casingStyleSettings->getCaseStyles();

        foreach ($caseStyles as $style) {
            if ($style->getLevel() === $getLevel) {
                return $style->getName();
            }
        }

        foreach ($caseStyles as $style) {
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
