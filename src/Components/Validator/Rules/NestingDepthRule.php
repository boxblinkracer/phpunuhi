<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

class NestingDepthRule implements RuleValidatorInterface
{

    /**
     * @var int
     */
    private $maxNestingLevel;


    /**
     * @param int $maxDepth
     */
    public function __construct(int $maxDepth)
    {
        $this->maxNestingLevel = $maxDepth;
    }


    /**
     * @return string
     */
    public function getRuleIdentifier(): string
    {
        return 'NESTING';
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

        if ($hierarchy->getDelimiter() === '') {
            return new ValidationResult([], []);
        }


        # this is always valid
        if ($this->maxNestingLevel <= 0) {
            return new ValidationResult([], []);
        }

        $tests = [];
        $errors = [];


        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $parts = explode($hierarchy->getDelimiter(), $translation->getKey());

                $currentLevels = count($parts);

                $testPassed = $currentLevels <= $this->maxNestingLevel;

                $tests[] = $this->buildTestEntry($locale, $translation->getKey(), $currentLevels, $testPassed);

                if ($testPassed) {
                    continue;
                }

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $errors[] = $this->buildError($locale, $identifier, $currentLevels);
            }
        }

        return new ValidationResult($tests, $errors);
    }

    /**
     * @param Locale $locale
     * @param string $translationKey
     * @param int $depthOfKey
     * @param bool $passed
     * @return ValidationTest
     */
    private function buildTestEntry(Locale $locale, string $translationKey, int $depthOfKey, bool $passed): ValidationTest
    {
        return new ValidationTest(
            $translationKey,
            $locale->getName(),
            'Test nesting level of key: ' . $translationKey,
            $locale->getFilename(),
            $locale->findLineNumber($translationKey),
            $this->getRuleIdentifier(),
            'Translation for key ' . $translationKey . ' has ' . $depthOfKey . ' levels. Maximum nesting level is: ' . $this->maxNestingLevel,
            $passed
        );
    }

    /**
     * @param Locale $locale
     * @param string $identifier
     * @param int $depthOfKey
     * @return ValidationError
     */
    private function buildError(Locale $locale, string $identifier, int $depthOfKey): ValidationError
    {
        return new ValidationError(
            $this->getRuleIdentifier(),
            'Maximum nesting level of ' . $this->maxNestingLevel . ' has been reached. Translation has ' . $depthOfKey . ' levels.',
            $locale->getName(),
            $locale->getFilename(),
            $identifier,
            $locale->findLineNumber($identifier)
        );
    }
}
