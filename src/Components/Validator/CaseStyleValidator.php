<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Output\OutputInterface;

class CaseStyleValidator implements ValidatorInterface
{

    /**
     * @var OutputInterface
     */
    private $output;


    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }


    /**
     * @param TranslationSet $set
     * @param StorageInterface $storage
     * @return bool
     * @throws \Exception
     */
    public function validate(TranslationSet $set, StorageInterface $storage): bool
    {
        $caseValidatorFactory = new CaseStyleValidatorFactory();

        $caseValidators = [];

        $hierarchy = $storage->getHierarchy();


        $isValid = true;

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

                    $this->output->writeln("[CASE-STYLE] Found invalid case-style in locale: " . $locale->getName());
                    if (!empty($locale->getFilename())) {
                        $this->output->writeln("  - " . $locale->getFilename());
                    }

                    $this->output->writeln('           [x]: ' . $translation->getKey());
                    $this->output->writeln("");

                    $isValid = false;
                    break;
                }
            }
        }

        return $isValid;
    }

}