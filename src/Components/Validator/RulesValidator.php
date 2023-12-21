<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Rules\DisallowedTextsRule;
use PHPUnuhi\Components\Validator\Rules\DuplicateContentRule;
use PHPUnuhi\Components\Validator\Rules\MaxKeyLengthRule;
use PHPUnuhi\Components\Validator\Rules\NestingDepthRule;
use PHPUnuhi\Components\Validator\Rules\RuleValidatorInterface;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\TranslationSet;

class RulesValidator implements ValidatorInterface
{

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return "RULE";
    }

    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        if ($set->getAllTranslationIDs() === []) {
            return new ValidationResult([], []);
        }

        $ruleValidators = [];

        foreach ($set->getRules() as $rule) {
            switch ($rule->getName()) {
                case Rules::NESTING_DEPTH:
                    $maxNestingLevel = (int)$rule->getValue();
                    $ruleValidators[] = new NestingDepthRule($maxNestingLevel);
                    break;

                case Rules::KEY_LENGTH:
                    $maxKeyLength = (int)$rule->getValue();
                    $ruleValidators[] = new MaxKeyLengthRule($maxKeyLength);
                    break;

                case Rules::DISALLOWED_TEXT:
                    $disallowedWords = (array)$rule->getValue();
                    $ruleValidators[] = new DisallowedTextsRule($disallowedWords);
                    break;

                case Rules::DUPLICATE_CONTENT:
                    $isDuplicateAllowed = (bool)$rule->getValue();
                    if (!$isDuplicateAllowed) {
                        $ruleValidators[] = new DuplicateContentRule();
                    }
                    break;
            }
        }


        $allTests = [];
        $allErrors = [];

        /** @var RuleValidatorInterface $validator */
        foreach ($ruleValidators as $validator) {
            $result = $validator->validate($set, $storage);

            $allTests = array_merge($allTests, $result->getTests());
            $allErrors = array_merge($allErrors, $result->getErrors());
        }

        return new ValidationResult(
            $allTests,
            $allErrors
        );
    }
}
