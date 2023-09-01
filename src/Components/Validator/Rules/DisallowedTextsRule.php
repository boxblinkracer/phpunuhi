<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class DisallowedTextsRule implements RuleValidatorInterface
{

    use StringTrait;

    /**
     * @var array<string>
     */
    private $disallowedWords;


    /**
     * @param string[] $disallowedWords
     */
    public function __construct(array $disallowedWords)
    {
        $this->disallowedWords = $disallowedWords;
    }


    /**
     * @return string
     */
    public function getRuleIdentifier(): string
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

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                $foundWord = null;
                foreach ($this->disallowedWords as $disallowedWord) {
                    if ($this->stringDoesContain($translation->getValue(), $disallowedWord)) {
                        $foundWord = $disallowedWord;
                        break;
                    }
                }

                $testPassed = ($foundWord === null);

                $tests[] = new ValidationTest(
                    $translation->getKey(),
                    $locale->getName(),
                    'Test against disallowed text for key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $locale->findLineNumber($translation->getKey()),
                    $this->getRuleIdentifier(),
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
                    $this->getRuleIdentifier(),
                    'Found disallowed text in key ' . $translation->getKey() . '. Value must not contain: ' . $foundWord,
                    $locale->getName(),
                    $locale->getFilename(),
                    $identifier,
                    $locale->findLineNumber($identifier)
                );
            }
        }

        return new ValidationResult($tests, $errors);
    }

}
