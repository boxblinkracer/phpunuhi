<?php

declare(strict_types=1);

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;

class EmptyContentValidator implements ValidatorInterface
{
    /**
     * @var AllowEmptyContent[]
     */
    private array $allowList;


    /**
     * @param AllowEmptyContent[] $allowList
     */
    public function __construct(array $allowList)
    {
        $this->allowList = $allowList;
    }



    public function getTypeIdentifier(): string
    {
        return 'EMPTY_CONTENT';
    }


    public function validate(TranslationSet $set, StorageInterface $storage): ValidationResult
    {
        $tests = [];

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {
                $testPassed = !$translation->isEmpty();

                if (!$testPassed) {

                    # check if we have an allow list entry
                    foreach ($this->allowList as $allowEntry) {
                        if ($allowEntry->getKey() === $translation->getKey() && $allowEntry->isLocaleAllowed($locale->getName())) {
                            $testPassed = true;
                            break;
                        }
                    }
                }

                if ($translation->getGroup() !== '') {
                    $identifier = $translation->getGroup() . ' (group) => ' . $translation->getKey();
                } else {
                    $identifier = $translation->getID();
                }

                $tests[] = $this->buildValidationTest($identifier, $locale, $translation, $testPassed);
            }
        }

        return new ValidationResult($tests);
    }


    private function buildValidationTest(string $identifier, Locale $locale, Translation $translation, bool $testPassed): ValidationTest
    {
        return new ValidationTest(
            $identifier,
            $locale->getName(),
            'Test existing translation for key: ' . $translation->getKey(),
            $locale->getFilename(),
            $locale->findLineNumber($translation->getKey()),
            $this->getTypeIdentifier(),
            'Translation for key ' . $translation->getKey() . ' does not have a value',
            $testPassed
        );
    }
}
