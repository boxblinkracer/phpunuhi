<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Models\Translation\TranslationSet;

class CaseStyleValidator implements ValidatorInterface
{


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


        $validationErrors = [];


        foreach ($set->getCasingStyles() as $style) {
            $caseValidators[] = $caseValidatorFactory->fromIdentifier($style);
        }

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                $isKeyCaseValid = true;

                if ($hierarchy->isMultiLevel()) {
                    $keyParts = explode($hierarchy->getDelimiter(), $translation->getKey());
                } else {
                    $keyParts = [$translation->getKey()];
                }

                if (!is_array($keyParts)) {
                    $keyParts = [];
                }

                $pathValid = true;

                foreach ($caseValidators as $caseValidator) {

                    $pathValid = true;
                    foreach ($keyParts as $part) {
                        $isCaseValid = $caseValidator->isValid($part);

                        if (!$isCaseValid) {
                            $pathValid = false;
                            break;
                        }
                    }

                    if ($pathValid) {
                        break;
                    }
                }

                if (!$pathValid) {
                    $isKeyCaseValid = false;
                }

                if (!$isKeyCaseValid) {
                    $validationErrors[] = new ValidationError(
                        'CASE-STYLE',
                        'Invalid case-style for key',
                        $locale->getName(),
                        $locale->getFilename(),
                        $translation->getKey()
                    );
                    break;
                }
            }
        }

        return new ValidationResult($validationErrors);
    }

}