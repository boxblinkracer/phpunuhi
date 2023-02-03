<?php

namespace PHPUnuhi\Components\Validator;

use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Output\OutputInterface;

class MixedStructureValidator implements ValidatorInterface
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

        $allKeys = $set->getAllTranslationIDs();

        foreach ($set->getLocales() as $locale) {

            $localeKeys = $locale->getTranslationIDs();

            # verify if our current locale has the same structure
            # as our global suite keys list
            $structureValid = $this->isStructureEqual($localeKeys, $allKeys);

            if (!$structureValid) {

                $this->output->writeln("[STRUCTURE] Found different structure in this file: ");
                $this->output->writeln("  - " . $locale->getName());
                if (!empty($locale->getFilename())) {
                    $this->output->writeln("    " . $locale->getFilename());
                }

                $filtered = $this->getDiff($localeKeys, $allKeys);

                foreach ($filtered as $key) {
                    $this->output->writeln('           [x]: ' . $key);
                }
                $this->output->writeln('');

                $isValid = false;
            }
        }

        return $isValid;
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
