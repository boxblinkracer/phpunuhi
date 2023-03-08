<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\TranslationSet;

class RuleValidatorKeyLength implements ValidatorInterface
{

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return 'KEY_LENGTH';
    }


    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $hierarchy = $storage->getHierarchy();


        $tests = [];
        $errors = [];

        $maxKeyLength = -1;

        foreach ($set->getRules() as $rule) {
            if ($rule->getName() === Rules::KEY_LENGTH) {
                $maxKeyLength = (int)$rule->getValue();
                break;
            }
        }

        if ($maxKeyLength === -1) {
            return new ValidationResult([], []);
        }

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                if ($hierarchy->isMultiLevel()) {
                    $parts = explode($hierarchy->getDelimiter(), $translation->getKey());
                    if (!is_array($parts)) {
                        $parts = [];
                    }
                } else {
                    $parts = [$translation->getKey()];
                }

                $invalidKey = null;
                foreach ($parts as $part) {

                    if (strlen($part) > $maxKeyLength) {
                        $invalidKey = $part;
                        break;
                    }
                }

                $testPassed = ($invalidKey === null);
                $invalidKey = (string)$invalidKey; # for output below

                $tests[] = new ValidationTest(
                    $locale->getName(),
                    'Test length of key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $this->getTypeIdentifier(),
                    'Maximum length of key ' . $invalidKey . ' has been reached. Length is ' . strlen($invalidKey) . ' of max allowed ' . $maxKeyLength . '.',
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
                    'Maximum length of key ' . $invalidKey . ' has been reached. Length is ' . strlen($invalidKey) . ' of max allowed ' . $maxKeyLength . '.',
                    $locale->getName(),
                    $locale->getFilename(),
                    $identifier
                );
            }
        }

        return new ValidationResult($tests, $errors);
    }

}