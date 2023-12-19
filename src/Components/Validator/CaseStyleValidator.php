<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
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
     * @throws CaseStyleNotFoundException
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $caseValidatorFactory = new CaseStyleValidatorFactory();

        $hierarchy = $storage->getHierarchy();

        $tests = [];
        $errors = [];

        $caseStyles = $set->getCasingStyles();

        $stylesHaveLevel = false;
        foreach ($caseStyles as $style) {
            if ($style->hasLevel()) {
                $stylesHaveLevel = true;
                break;
            }
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

                if (count($caseStyles) <= 0) {
                    $pathValid = true;
                }

                $partLevel = 0;

                foreach ($keyParts as $part) {

                    $invalidKeyPart = $part;

                    foreach ($caseStyles as $caseStyle) {

                        if ($caseStyle->hasLevel() && $caseStyle->getLevel() !== $partLevel) {
                            continue;
                        }

                        # if we have levels somewhere,
                        # then make sure global keys are only checked if we dont have specific styles for our level
                        if ($stylesHaveLevel && !$caseStyle->hasLevel()) {
                            # check if we have another style for our level
                            foreach ($caseStyles as $tmpStyle) {
                                if (!$tmpStyle->hasLevel()) {
                                    continue;
                                }
                                if ($tmpStyle->getLevel() !== $partLevel) {
                                    continue;
                                }
                                continue 2;
                            }
                        }

                        $caseValidator = $caseValidatorFactory->fromIdentifier($caseStyle->getName());

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

                    $partLevel++;
                }

                if (!$pathValid) {
                    $isKeyCaseValid = false;
                }

                $testPassed = $isKeyCaseValid;

                $tests[] = new ValidationTest(
                    $translation->getKey(),
                    $locale->getName(),
                    'Test case-style of key: ' . $translation->getKey(),
                    $locale->getFilename(),
                    $locale->findLineNumber($translation->getKey()),
                    $this->getTypeIdentifier(),
                    'Translation key ' . $translation->getKey() . ' has part with invalid case-style: ' . $invalidKeyPart . ' at level: ' . $partLevel,
                    $testPassed
                );

                if (!$testPassed) {
                    $errors[] = new ValidationError(
                        $this->getTypeIdentifier(),
                        'Invalid case-style for key: ' . $invalidKeyPart . ' at level: ' . $partLevel,
                        $locale->getName(),
                        $locale->getFilename(),
                        $translation->getKey(),
                        $locale->findLineNumber($translation->getKey())
                    );
                    break;
                }
            }
        }

        return new ValidationResult($tests, $errors);
    }

}