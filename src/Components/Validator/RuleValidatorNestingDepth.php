<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\TranslationSet;

class RuleValidatorNestingDepth implements ValidatorInterface
{

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
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

        $tests = [];
        $errors = [];

        $maxNestingLevel = -1;

        foreach ($set->getRules() as $rule) {
            if ($rule->getName() === Rules::NESTING_DEPTH) {
                $maxNestingLevel = (int)$rule->getValue();
                break;
            }
        }

        if ($maxNestingLevel === -1) {
            return new ValidationResult([], []);
        }

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                $parts = explode($hierarchy->getDelimiter(), $translation->getKey());

                if (!is_array($parts)) {
                    $parts = [];
                }

                $currentLevels = count($parts);

                $testPassed = $currentLevels <= $maxNestingLevel;

                $tests[] = new ValidationTest(
                    $locale->getName(),
                    'Test nesting level of key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $this->getTypeIdentifier(),
                    'Translation for key ' . $translation->getKey() . ' has ' . $currentLevels . ' levels. Maximum nesting level is: ' . $maxNestingLevel,
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

                $errors[] = new ValidationError(
                    $this->getTypeIdentifier(),
                    'Maximum nesting level of ' . $maxNestingLevel . ' has been reached. Translation has ' . $currentLevels . ' levels.',
                    $locale->getName(),
                    $locale->getFilename(),
                    $identifier
                );
            }
        }

        return new ValidationResult($tests, $errors);
    }

}