<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Models\Translation\TranslationSet;

class MissingStructureValidator implements ValidatorInterface
{

    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $allKeys = $set->getAllTranslationIDs();

        $validationErrors = [];

        foreach ($set->getLocales() as $locale) {

            $localeKeys = $locale->getTranslationIDs();

            # verify if our current locale has the same structure
            # as our global suite keys list
            $structureValid = $this->isStructureEqual($localeKeys, $allKeys);

            if (!$structureValid) {

                $filtered = $this->getDiff($localeKeys, $allKeys);

                foreach ($filtered as $key) {
                    $validationErrors[] = new ValidationError(
                        'STRUCTURE',
                        'Found missing structure in locale',
                        $locale->getName(),
                        $locale->getFilename(),
                        $key
                    );
                }
            }
        }

        return new ValidationResult($validationErrors);
    }


    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    private function isStructureEqual($a, $b)
    {
        return (
            is_array($b)
            && is_array($a)
            && count($a) === count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     * @return array<mixed>
     */
    private function getDiff(array $a, array $b): array
    {
        $diffA = array_diff($a, $b);
        $diffB = array_diff($b, $a);

        return array_merge($diffA, $diffB);
    }

}
