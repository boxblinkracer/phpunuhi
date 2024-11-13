<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class EmptyContentRule implements RuleValidatorInterface
{
    public const IDENTIFIER = 'EMPTY_CONTENT';


    public function getRuleIdentifier(): string
    {
        return self::IDENTIFIER;
    }


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $tests = [];

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $tests[] = $this->buildTestEntry(
                    $locale,
                    $translation->getKey(),
                    true
                );
            }
        }

        return new ValidationResult($tests);
    }


    private function buildTestEntry(Locale $locale, string $translationKey, bool $passed): ValidationTest
    {
        return new ValidationTest(
            $translationKey,
            $locale,
            'Testing for empty content of key: ' . $translationKey,
            $locale->getFilename(),
            $locale->findLineNumber($translationKey),
            $this->getRuleIdentifier(),
            'Content of key ' . $translationKey . ' has been found with correct empty value in locale: ' . $locale->getName(),
            $passed
        );
    }
}
