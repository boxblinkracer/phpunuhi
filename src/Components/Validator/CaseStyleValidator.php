<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class CaseStyleValidator implements ValidatorInterface
{
    use StringTrait;


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
        $hierarchy = $storage->getHierarchy();

        $tests = [];
        $errors = [];

        $caseStyles = $set->getCasingStyleSettings()->getCaseStyles();
        $ignoreKeys = $set->getCasingStyleSettings()->getIgnoreKeys();

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $isKeyCaseValid = true;
                $invalidKeyPart = '';
                $isCurrentKeyValid = true;

                # we have a full key like root.sub.lblTitle
                # and we want to split it into parts and separately check the cases of the hierarchy levels
                # sample: root.sub.lblTitle => [root, sub, lblTitle]
                $keyParts = $this->getKeyParts($translation->getKey(), $hierarchy);

                $partLevel = 0;

                foreach ($keyParts as $part) {
                    $isPartValid = $this->verifyLevel($part, $partLevel, $caseStyles);

                    if (!$isPartValid) {
                        $invalidKeyPart = $part;
                        $isCurrentKeyValid = false;
                        break;
                    }

                    $partLevel++;
                }


                # if it's somehow not valid
                # then make sure to also check ouf ignore list
                # then it might be valid :)
                if (!$isCurrentKeyValid) {
                    # sample: $part => root.sub.IGNORE_THIS (fully qualified)
                    # also check ignore list
                    foreach ($ignoreKeys as $ignoreKey) {
                        if ($ignoreKey->isFullyQualifiedPath()) {
                            if ($translation->getKey() === $ignoreKey->getKey()) {
                                $isCurrentKeyValid = true;
                                break;
                            }
                        } elseif ($this->stringDoesContain($translation->getKey(), $ignoreKey->getKey())) {
                            $isCurrentKeyValid = true;
                            break;
                        }
                    }
                }

                if (!$isCurrentKeyValid) {
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
                }
            }
        }

        return new ValidationResult($tests, $errors);
    }

    /**
     * @param string $key
     * @param StorageHierarchy $hierarchy
     * @return string[]
     */
    private function getKeyParts(string $key, StorageHierarchy $hierarchy): array
    {
        if ($hierarchy->isNestedStorage() && $hierarchy->getDelimiter() !== '') {
            return explode($hierarchy->getDelimiter(), $key);
        }

        return [$key];
    }


    /**
     * @param string $part
     * @param int $level
     * @param CaseStyle[] $caseStyles
     * @return bool
     * @throws CaseStyleNotFoundException
     */
    private function verifyLevel(string $part, int $level, array $caseStyles): bool
    {
        $caseStyles = $this->getCaseStylesForLevel($level, $caseStyles);

        # if no case styles are defined for this level
        # then it's valid
        if ($caseStyles === []) {
            return true;
        }

        $caseValidatorFactory = new CaseStyleValidatorFactory();

        foreach ($caseStyles as $caseStyle) {
            $isValid = $caseValidatorFactory->fromIdentifier($caseStyle->getName())->isValid($part);

            if ($isValid) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $level
     * @param CaseStyle[] $caseStyles
     * @return CaseStyle[]
     */
    private function getCaseStylesForLevel(int $level, array $caseStyles): array
    {
        $foundStyles = [];

        foreach ($caseStyles as $caseStyle) {

            # first check for a specific level
            # if we have a specific level then we take this one
            # and immediately return
            if ($caseStyle->hasLevel() && $caseStyle->getLevel() === $level) {
                $foundStyles[] = $caseStyle;
                return $foundStyles;
            }

            # if we have not found a specific level
            # then we take all the ones that don't have a level
            if (!$caseStyle->hasLevel()) {
                $foundStyles[] = $caseStyle;
            }
        }
        return $foundStyles;
    }
}
