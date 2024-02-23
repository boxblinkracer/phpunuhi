<?php

namespace PHPUnuhi\Components\Validator\Rules;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\TranslationSet;

class MaxKeyLengthRule implements RuleValidatorInterface
{

    /**
     * @var int
     */
    private $maxKeyLength;


    /**
     * @param int $maxKeyLength
     */
    public function __construct(int $maxKeyLength)
    {
        $this->maxKeyLength = $maxKeyLength;
    }


    /**
     * @return string
     */
    public function getRuleIdentifier(): string
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


        # this is always valid
        if ($this->maxKeyLength <= 0) {
            return new ValidationResult([]);
        }

        $tests = [];

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                if ($hierarchy->isNestedStorage() && ($hierarchy->getDelimiter() !== '' && $hierarchy->getDelimiter() !== '0')) {
                    $parts = explode($hierarchy->getDelimiter(), $translation->getKey());
                } else {
                    $parts = [$translation->getKey()];
                }

                $invalidKey = null;
                foreach ($parts as $part) {
                    if (strlen($part) > $this->maxKeyLength) {
                        $invalidKey = $part;
                        break;
                    }
                }

                $testPassed = ($invalidKey === null);
                $invalidKey = (string)$invalidKey; # for output below

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $tests[] = new ValidationTest(
                    $identifier,
                    $locale->getName(),
                    "Test length of key '" . $translation->getKey(),
                    $locale->getFilename(),
                    $locale->findLineNumber($translation->getKey()),
                    $this->getRuleIdentifier(),
                    "Maximum length of " . $this->maxKeyLength . " for key '" . $invalidKey . "' has been reached. Length is " . strlen($invalidKey),
                    $testPassed
                );
            }
        }

        return new ValidationResult($tests);
    }
}
