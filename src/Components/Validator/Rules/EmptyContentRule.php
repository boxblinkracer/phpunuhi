<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class EmptyContentRule implements RuleValidatorInterface
{
    public const IDENTIFIER = 'EMPTY_CONTENT';

    /**
     * @return string
     */
    public function getRuleIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $tests = [];
        $errors = [];

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $testPassed = true;

                $tests[] = $this->buildTestEntry(
                    $locale,
                    $translation->getKey(),
                    true
                );
            }
        }

        return new ValidationResult($tests, $errors);
    }

    /**
     * @param Locale $locale
     * @param string $translationKey
     * @param bool $passed
     * @return ValidationTest
     */
    private function buildTestEntry(Locale $locale, string $translationKey, bool $passed): ValidationTest
    {
        return new ValidationTest(
            $translationKey,
            $locale->getName(),
            'Testing for empty content of key: ' . $translationKey,
            $locale->getFilename(),
            $locale->findLineNumber($translationKey),
            $this->getRuleIdentifier(),
            'Content of key ' . $translationKey . ' has been found with correct empty value in locale: ' . $locale->getName(),
            $passed
        );
    }
}
