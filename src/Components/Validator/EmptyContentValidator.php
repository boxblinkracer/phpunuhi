<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyContentValidator implements ValidatorInterface
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
     */
    public function validate(TranslationSet $set, StorageInterface $storage): bool
    {
        $isValid = true;

        foreach ($set->getLocales() as $locale) {
            foreach ($locale->getTranslations() as $translation) {

                if ($translation->isEmpty()) {
                    $this->output->writeln("[EMPTY] Found empty translation in locale: " . $locale->getName());
                    if (!empty($locale->getFilename())) {
                        $this->output->writeln("  - " . $locale->getFilename());
                    }

                    if ($translation->getGroup() !== '') {
                        $this->output->writeln('           [x]: ' . $translation->getGroup() . ' (group) => ' . $translation->getKey());
                    } else {
                        $this->output->writeln('           [x]: ' . $translation->getID());
                    }

                    $this->output->writeln('');
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

}
