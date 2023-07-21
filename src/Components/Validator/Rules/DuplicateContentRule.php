<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class DuplicateContentRule implements RuleValidatorInterface
{


    /**
     * @return string
     */
    public function getRuleIdentifier(): string
    {
        return 'DUPLICATE_CONTENT';
    }


    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $hierarchy = $storage->getHierarchy();

        if (!$hierarchy->isMultiLevel()) {
            return new ValidationResult([], []);
        }

        $tests = [];
        $errors = [];


        foreach ($set->getLocales() as $locale) {

            $existingValues = [];

            foreach ($locale->getTranslations() as $translation) {

                $testPassed = false;

                if (!in_array($translation->getValue(), $existingValues, true)) {
                    $existingValues[] = $translation->getValue();
                    $testPassed = true;
                }

                $tests[] = $this->buildTestEntry(
                    $locale,
                    $translation->getKey(),
                    $testPassed
                );

                if ($testPassed) {
                    continue;
                }

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $errors[] = $this->buildError($locale, $identifier);
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
            'Testing for duplicate content of key: ' . $translationKey,
            $locale->getFilename(),
            $this->getRuleIdentifier(),
            'Content of key ' . $translationKey . ' has been found multiple times within locale: ' . $locale->getName(),
            $passed
        );
    }

    /**
     * @param Locale $locale
     * @param string $identifier
     * @return ValidationError
     */
    private function buildError(Locale $locale, string $identifier): ValidationError
    {
        return new ValidationError(
            $this->getRuleIdentifier(),
            'Content of key ' . $identifier . ' has been found multiple times within locale: ' . $locale->getName(),
            $locale->getName(),
            $locale->getFilename(),
            $identifier
        );
    }

}