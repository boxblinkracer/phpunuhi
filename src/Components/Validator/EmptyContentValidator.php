<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\TranslationSet;

class EmptyContentValidator implements ValidatorInterface
{


    /**
     * @var AllowEmptyContent[]
     */
    private $allowList;


    /**
     * @param AllowEmptyContent[] $allowList
     */
    public function __construct(array $allowList)
    {
        $this->allowList = $allowList;
    }


    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return 'EMPTY_CONTENT';
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
                $testPassed = !$translation->isEmpty();

                $tests[] = new ValidationTest(
                    $translation->getKey(),
                    $locale->getName(),
                    'Test existing translation of key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $locale->findLineNumber($translation->getKey()),
                    $this->getTypeIdentifier(),
                    'Translation for key ' . $translation->getKey() . ' does not have a value',
                    $testPassed
                );

                if (!$testPassed) {
                    # check if we have an allow list entry
                    foreach ($this->allowList as $allowEntry) {
                        if ($allowEntry->getKey() === $translation->getKey() && $allowEntry->isLocaleAllowed($locale->getName())) {
                            $testPassed = true;
                            break;
                        }
                    }
                }

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
                    'Found empty translation',
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
