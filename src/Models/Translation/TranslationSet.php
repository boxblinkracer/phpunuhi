<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Translation;

use Exception;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Configuration\Attribute;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;
use PHPUnuhi\Services\Placeholder\PlaceholderExtractor;

class TranslationSet
{
    private string $name;

    private string $format;

    private Protection $protection;

    /**
     * @var Attribute[]
     */
    private array $attributes;

    /**
     * @var Locale[]
     */
    private array $locales;

    private Filter $filter;

    private CaseStyleSetting $casingStyleSettings;

    /**
     * @var Rule[]
     */
    private array $rules;

    private PlaceholderEncoder $placeHolderEncoder;

    private PlaceholderExtractor $placeholderExtractor;


    /**
     * @param Locale[] $locales
     * @param Attribute[] $attributes
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

        $this->placeHolderEncoder = new PlaceholderEncoder();
        $this->placeholderExtractor = new PlaceholderExtractor();
    }



    public function getName(): string
    {
        return $this->name;
    }


    public function getFormat(): string
    {
        return $this->format;
    }


    public function getProtection(): Protection
    {
        return $this->protection;
    }


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


    public function getAttributeValue(string $name): string
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute->getValue();
            }
        }

        return '';
    }


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
     * @throws Exception
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


    public function getCasingStyleSettings(): CaseStyleSetting
    {
        return $this->casingStyleSettings;
    }


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


    public function getEncodedValue(string $text): string
    {
        $foundPlaceholders = $this->findPlaceholders($text);

        if ($foundPlaceholders === []) {
            return $text;
        }

        return $this->placeHolderEncoder->encode($text, $foundPlaceholders);
    }

    public function getDecodedText(string $originalText, string $encodedText): string
    {
        $foundPlaceholders = $this->findPlaceholders($originalText);

        if ($foundPlaceholders === []) {
            return $encodedText;
        }

        return $this->placeHolderEncoder->decode($encodedText, $foundPlaceholders);
    }

    /**
     * @return Placeholder[]
     */
    public function findPlaceholders(string $text): array
    {
        $foundPlaceholders = [];

        foreach ($this->getProtection()->getMarkers() as $marker) {
            # search for all possible placeholders that exist, like %productName%
            # we must not translate them (happens with DeepL, ...)
            $markerPlaceholders = $this->placeholderExtractor->extract($text, $marker->getStart(), $marker->getEnd());

            $foundPlaceholders = array_merge($foundPlaceholders, $markerPlaceholders);
        }

        foreach ($this->getProtection()->getTerms() as $term) {
            # just add these as placeholders
            $foundPlaceholders[] = new Placeholder($term);
        }

        return $foundPlaceholders;
    }
}
