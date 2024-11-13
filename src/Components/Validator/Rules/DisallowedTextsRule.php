<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
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
    private array $disallowedWords;


    /**
     * @param string[] $disallowedWords
     */
    public function __construct(array $disallowedWords)
    {
        $this->disallowedWords = $disallowedWords;
    }



    public function getRuleIdentifier(): string
    {
        return 'DISALLOWED_TEXT';
    }


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $tests = [];

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

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $tests[] = new ValidationTest(
                    $identifier,
                    $locale->getName(),
                    "Test against disallowed texts for key: '" . $translation->getKey(),
                    $locale->getFilename(),
                    $locale->findLineNumber($translation->getKey()),
                    $this->getRuleIdentifier(),
                    "Found disallowed text in key '" . $translation->getKey() . "'. Value must not contain: '" . $foundWord . "'",
                    $testPassed
                );
            }
        }

        return new ValidationResult($tests);
    }
}
