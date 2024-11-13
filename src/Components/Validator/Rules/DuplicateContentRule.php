<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\DuplicateContent\DuplicateContent;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class DuplicateContentRule implements RuleValidatorInterface
{
    /**
     * @var DuplicateContent[]
     */
    private array $localeSettings;


    /**
     * @param DuplicateContent[] $localeSettings
     */
    public function __construct(array $localeSettings)
    {
        $this->localeSettings = $localeSettings;
    }



    public function getRuleIdentifier(): string
    {
        return 'DUPLICATE_CONTENT';
    }



    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        if ($this->localeSettings === []) {
            return new ValidationResult([]);
        }

        $storage->getHierarchy();

        $tests = [];

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

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $tests[] = $this->buildTestEntry(
                    $locale,
                    $identifier,
                    $testPassed
                );
            }
        }

        return new ValidationResult($tests);
    }


    private function requiresDuplicateContent(string $locale): bool
    {
        foreach ($this->localeSettings as $localeSetting) {
            if ($localeSetting->getLocale() === $locale) {
                return !$localeSetting->isDuplicateAllowed();
            }
        }

        # that's the default
        foreach ($this->localeSettings as $localeSetting) {
            if ($localeSetting->getLocale() === '*') {
                return !$localeSetting->isDuplicateAllowed();
            }
        }

        return false;
    }



    private function buildTestEntry(Locale $locale, string $translationKey, bool $passed): ValidationTest
    {
        return new ValidationTest(
            $translationKey,
            $locale,
            "Testing for duplicate content in key: '" . $translationKey . "'",
            $locale->getFilename(),
            $locale->findLineNumber($translationKey),
            $this->getRuleIdentifier(),
            "Content of key '" . $translationKey . "' has been found multiple times within locale '" . $locale->getName() . "'",
            $passed
        );
    }
}
