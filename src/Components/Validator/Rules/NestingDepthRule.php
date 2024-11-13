<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class NestingDepthRule implements RuleValidatorInterface
{
    private int $maxNestingLevel;



    public function __construct(int $maxDepth)
    {
        $this->maxNestingLevel = $maxDepth;
    }



    public function getRuleIdentifier(): string
    {
        return 'NESTING';
    }



    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $hierarchy = $storage->getHierarchy();

        if (!$hierarchy->isNestedStorage()) {
            return new ValidationResult([]);
        }

        if ($hierarchy->getDelimiter() === '') {
            return new ValidationResult([]);
        }


        # this is always valid
        if ($this->maxNestingLevel <= 0) {
            return new ValidationResult([]);
        }

        $tests = [];

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $parts = explode($hierarchy->getDelimiter(), $translation->getKey());

                $currentLevels = count($parts);

                $testPassed = $currentLevels <= $this->maxNestingLevel;

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $tests[] = $this->buildTestEntry($locale, $identifier, $currentLevels, $testPassed);
            }
        }

        return new ValidationResult($tests);
    }


    private function buildTestEntry(Locale $locale, string $translationKey, int $depthOfKey, bool $passed): ValidationTest
    {
        return new ValidationTest(
            $translationKey,
            $locale,
            "Test nesting-depth of key '" . $translationKey,
            $locale->getFilename(),
            $locale->findLineNumber($translationKey),
            $this->getRuleIdentifier(),
            'Maximum nesting depth of ' . $this->maxNestingLevel . ' has been reached. Key has ' . $depthOfKey . ' levels',
            $passed
        );
    }
}
