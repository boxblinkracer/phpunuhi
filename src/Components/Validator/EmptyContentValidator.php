<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class EmptyContentValidator implements ValidatorInterface
{
    /**
     * @var AllowEmptyContent[]
     */
    private array $allowList;


    /**
     * @param AllowEmptyContent[] $allowList
     */
    public function __construct(array $allowList)
    {
        $this->allowList = $allowList;
    }


    public function getTypeIdentifier(): string
    {
        return 'EMPTY_CONTENT';
    }


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $tests = [];

        # first search if we have a base locale
        $baseLocale = null;

        foreach ($set->getLocales() as $locale) {
            if ($locale->isBase()) {
                $baseLocale = $locale;
                break;
            }
        }

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $testPassed = !$translation->isEmpty();
                $baseLocaleWarning = false;

                if (!$testPassed) {
                    # check if we have an allow list entry
                    foreach ($this->allowList as $allowEntry) {
                        if ($allowEntry->getKey() === $translation->getKey() && $allowEntry->isLocaleAllowed($locale->getName())) {
                            $testPassed = true;
                            break;
                        }
                    }
                }


                # we haven't the required key in our current locale
                # let's figure out if we have a base locale and if it's also missing there
                # if so, then let the user know
                if (!$testPassed && ($baseLocale instanceof Locale && $locale->getName() !== $baseLocale->getName())) {
                    try {
                        $baseLocaleTranslation = $baseLocale->findTranslation($translation->getKey());

                        # if this is empty, then we also want to warn, that the base local is also empty
                        # and that it might be just fine...
                        $baseLocaleWarning = $baseLocaleTranslation->isEmpty();
                    } catch (TranslationNotFoundException $e) {
                        $baseLocaleWarning = true;
                    }
                }

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $tests[] = $this->buildValidationTest($identifier, $locale, $translation, $baseLocaleWarning, $testPassed);
            }
        }

        return new ValidationResult($tests);
    }


    private function buildValidationTest(string $identifier, Locale $locale, Translation $translation, bool $baseLocaleDeprecationInfo, bool $testPassed): ValidationTest
    {
        $suffix = '';

        if ($baseLocaleDeprecationInfo) {
            $suffix = ' (your base locale is also missing this translation. Maybe this is an expected behavior?)';
        }

        if ($locale->isBase()) {
            $suffix = ' (this is a base locale. Maybe this is an expected behavior?)';
        }

        return new ValidationTest(
            $identifier,
            $locale,
            'Test existing translation for key: ' . $translation->getKey(),
            $locale->getFilename(),
            $locale->findLineNumber($translation->getKey()),
            $this->getTypeIdentifier(),
            'Translation for key ' . $translation->getKey() . ' does not have a value.' . $suffix,
            $testPassed
        );
    }
}
