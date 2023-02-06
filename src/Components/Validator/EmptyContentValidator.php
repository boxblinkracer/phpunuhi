<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\TranslationSet;

class EmptyContentValidator implements ValidatorInterface
{

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

                $tests[] = new ValidationTest(
                    $locale->getName(),
                    'Test existing translation of key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    'EMPTY',
                    !$translation->isEmpty()
                );

                if ($translation->isEmpty()) {

                    if ($translation->getGroup() !== '') {
                        $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                    } else {
                        $identifier = $translation->getID();
                    }

                    $errors[] = new ValidationError(
                        'EMPTY',
                        'Found empty translation',
                        $locale->getName(),
                        $locale->getFilename(),
                        $identifier
                    );
                }
            }
        }

        return new ValidationResult($tests, $errors);
    }

}
