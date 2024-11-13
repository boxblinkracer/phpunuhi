<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class MissingStructureValidator implements ValidatorInterface
{
    public function getTypeIdentifier(): string
    {
        return 'STRUCTURE';
    }


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $allKeys = $set->getAllTranslationIDs();

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
            $localeKeys = $locale->getTranslationIDs();

            # verify if our current locale has the same structure
            # as our global suite keys list
            $structureValid = $this->isStructureEqual($localeKeys, $allKeys);

            $same = $this->getSame($localeKeys, $allKeys);

            if (!$structureValid) {
                $filtered = $this->getDiff($localeKeys, $allKeys);

                foreach ($filtered as $key) {
                    $baseLocaleWarning = false;

                    # we haven't the required key in our current locale
                    # let's figure out if we have a base locale and if it's also missing there
                    # if so, then let the user know
                    if ($baseLocale instanceof Locale && $locale->getName() !== $baseLocale->getName()) {
                        try {
                            $baseLocale->findTranslation($key);
                        } catch (TranslationNotFoundException $e) {
                            $baseLocaleWarning = true;
                        }
                    }
                    $tests[] = $this->buildValidationTest($key, $locale, $baseLocaleWarning, false);
                }
            }

            foreach ($same as $key) {
                $tests[] = $this->buildValidationTest($key, $locale, false, true);
            }
        }

        return new ValidationResult($tests);
    }


    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     */
    private function isStructureEqual(array $a, array $b): bool
    {
        return (count($a) === count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     * @return array<mixed>
     */
    private function getDiff(array $a, array $b): array
    {
        $diffA = array_diff($a, $b);
        $diffB = array_diff($b, $a);

        return array_merge($diffA, $diffB);
    }

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     * @return array<mixed>
     */
    private function getSame(array $a, array $b): array
    {
        $diffA = array_intersect($a, $b);
        $diffB = array_intersect($b, $a);

        return array_merge($diffA, $diffB);
    }


    private function buildValidationTest(string $key, Locale $locale, bool $baseLocaleDeprecationInfo, bool $success): ValidationTest
    {
        $suffix = '';

        if ($baseLocaleDeprecationInfo) {
            $suffix = ' (your base locale is also missing this key. Maybe this is an expected behavior?)';
        }

        if ($locale->isBase()) {
            $suffix = ' (this is a base locale. Maybe this is an expected behavior?)';
        }

        return new ValidationTest(
            $key,
            $locale,
            'Test structure of key: ' . $key,
            $locale->getFilename(),
            $locale->findLineNumber($key),
            $this->getTypeIdentifier(),
            'Found missing structure in locale. Key is missing: ' . $key . $suffix,
            $success
        );
    }
}
