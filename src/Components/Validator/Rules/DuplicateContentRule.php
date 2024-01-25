<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\DuplicateContent\DuplicateContent;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class DuplicateContentRule implements RuleValidatorInterface
{

    /**
     * @var DuplicateContent[]
     */
    private $localeSettings;


    /**
     * @param DuplicateContent[] $localeSettings
     */
    public function __construct(array $localeSettings)
    {
        $this->localeSettings = $localeSettings;
    }


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
        if (count($this->localeSettings) === 0) {
            return new ValidationResult([], []);
        }

        $storage->getHierarchy();

        $tests = [];
        $errors = [];


        foreach ($set->getLocales() as $locale) {
            if (!$this->requiresDuplicateContent($locale->getName())) {
                continue;
            }

            $existingValues = [];

            foreach ($locale->getTranslations() as $translation) {
                $testPassed = false;

                if (!in_array($translation->getValue(), $existingValues, true)) {
                    $existingValues[] = $translation->getValue();
                    $testPassed = true;
                }

                # if its empty we dont count it
                if ($translation->getValue() === '') {
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
     * @param string $locale
     * @return bool
     */
    private function requiresDuplicateContent(string $locale): bool
    {
        foreach ($this->localeSettings as $localeSetting) {
            if ($localeSetting->getLocale() === $locale) {
                return !$localeSetting->isDuplicateAllowed();
            }
        }


        # thats the default
        foreach ($this->localeSettings as $localeSetting) {
            if ($localeSetting->getLocale() === '*') {
                return !$localeSetting->isDuplicateAllowed();
            }
        }

        return false;
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
            $locale->findLineNumber($translationKey),
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
            $identifier,
            $locale->findLineNumber($identifier)
        );
    }
}
