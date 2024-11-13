<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\StringTrait;

class CaseStyleValidator implements ValidatorInterface
{
    use StringTrait;



    public function getTypeIdentifier(): string
    {
        return 'CASE_STYLE';
    }

    /**
     * @throws CaseStyleNotFoundException
     */
    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $hierarchy = $storage->getHierarchy();

        $tests = [];

        $caseStyles = $set->getCasingStyleSettings()->getCaseStyles();
        $ignoreKeys = $set->getCasingStyleSettings()->getIgnoreKeys();

        $allowedCaseStylesText = implode(', ', array_map(function (CaseStyle $caseStyle): string {
            return $caseStyle->getName();
        }, $caseStyles));

        if ($caseStyles !== []) {
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
                        "Test case-style of key '" . $translation->getKey() . "' to be one of: " . $allowedCaseStylesText,
                        $locale->getFilename(),
                        $locale->findLineNumber($translation->getKey()),
                        $this->getTypeIdentifier(),
                        "Invalid case-style for part '" . $invalidKeyPart . "' in key '" . $translation->getKey() . "' at level: " . $partLevel,
                        $testPassed
                    );
                }
            }
        }

        return new ValidationResult($tests);
    }

    /**
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
     * @param CaseStyle[] $caseStyles
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
