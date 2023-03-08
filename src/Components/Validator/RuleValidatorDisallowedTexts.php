<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class RuleValidatorDisallowedTexts implements ValidatorInterface
{

    use StringTrait;


    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return 'DISALLOWED_TEXT';
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

        $disallowedWords = [];

        foreach ($set->getRules() as $rule) {
            if ($rule->getName() === Rules::DISALLOWED_TEXT) {
                $disallowedWords = (array)$rule->getValue();
                break;
            }
        }

        if (count($disallowedWords) === 0) {
            return new ValidationResult([], []);
        }

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                $foundWord = null;
                foreach ($disallowedWords as $disallowedWord) {
                    if ($this->stringDoesContain($translation->getValue(), $disallowedWord)) {
                        $foundWord = $disallowedWord;
                        break;
                    }
                }

                $testPassed = ($foundWord === null);

                $tests[] = new ValidationTest(
                    $locale->getName(),
                    'Test against disallowed text for key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $this->getTypeIdentifier(),
                    'Translation for key ' . $translation->getKey() . ' has disallowed text: ' . (string)$foundWord,
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
                    'Found disallowed text in key ' . $translation->getKey() . '. Value must not contain: ' . $foundWord,
                    $locale->getName(),
                    $locale->getFilename(),
                    $identifier
                );
            }
        }

        return new ValidationResult($tests, $errors);
    }

}
