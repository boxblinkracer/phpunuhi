<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\TranslationSet;

class CaseStyleValidator implements ValidatorInterface
{

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return 'CASE_STYLE';
    }

    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return ValidationResult
     * @throws CaseStyle\Exception\CaseStyleNotFoundException
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $caseValidatorFactory = new CaseStyleValidatorFactory();

        $caseValidators = [];

        $hierarchy = $storage->getHierarchy();


        $tests = [];
        $errors = [];


        foreach ($set->getCasingStyles() as $style) {
            $caseValidators[] = $caseValidatorFactory->fromIdentifier($style);
        }

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                $isKeyCaseValid = true;
                $invalidKeyPart = '';

                if ($hierarchy->isMultiLevel()) {
                    $keyParts = explode($hierarchy->getDelimiter(), $translation->getKey());
                } else {
                    $keyParts = [$translation->getKey()];
                }

                if (!is_array($keyParts)) {
                    $keyParts = [];
                }

                $pathValid = false;

                foreach ($keyParts as $part) {

                    foreach ($caseValidators as $caseValidator) {

                        $isPartValid = $caseValidator->isValid($part);

                        if ($isPartValid) {
                            $pathValid = true;
                            $invalidKeyPart = '';
                            break;
                        }

                        $pathValid = false;
                        $invalidKeyPart = $part;
                    }

                    if (!$pathValid) {
                        break;
                    }
                }

                if (!$pathValid) {
                    $isKeyCaseValid = false;
                }

                $tests[] = new ValidationTest(
                    $locale->getName(),
                    'Test case-style of key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $this->getTypeIdentifier(),
                    'Translation key ' . $translation->getKey() . ' has part with invalid case-style: ' . $invalidKeyPart,
                    $isKeyCaseValid
                );

                if (!$isKeyCaseValid) {
                    $errors[] = new ValidationError(
                        $this->getTypeIdentifier(),
                        'Invalid case-style for key: ' . $invalidKeyPart,
                        $locale->getName(),
                        $locale->getFilename(),
                        $translation->getKey()
                    );
                    break;
                }
            }
        }

        return new ValidationResult($tests, $errors);
    }

}