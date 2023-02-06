<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
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

        $tests = [];
        $errors = [];

        foreach ($set->getLocales() as $locale) {

            $localeKeys = $locale->getTranslationIDs();

            # verify if our current locale has the same structure
            # as our global suite keys list
            $structureValid = $this->isStructureEqual($localeKeys, $allKeys);


            $same = $this->getSame($localeKeys, $allKeys);

            if (!$structureValid) {

                $filtered = $this->getDiff($localeKeys, $allKeys);

                foreach ($filtered as $key) {
                    $errors[] = new ValidationError(
                        'STRUCTURE',
                        'Found missing structure in locale',
                        $locale->getName(),
                        $locale->getFilename(),
                        $key
                    );

                    $tests[] = new ValidationTest(
                        $locale->getName(),
                        'Text structure of key: ' . $key,
                        $locale->getFilename(),
                        false
                    );
                }
            }

            foreach ($same as $key) {
                $tests[] = new ValidationTest(
                    $locale->getName(),
                    'Text structure of key: ' . $key,
                    $locale->getFilename(),
                    true
                );
            }
        }

        return new ValidationResult($tests, $errors);
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

    /**
     * @param array<mixed> $a
     * @param array<mixed> $b
     * @return array<mixed>
     */
    private function getSame(array $a, array $b): array
    {
        $diffA = array_intersect($a, $b);
        $diffB = array_intersect($b, $a);

        return array_merge($diffA, $diffB);
    }

}
