<?php

declare(strict_types=1);

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslationSetCliFacade
{
    private SymfonyStyle $io;


    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * @param TranslationSet[] $translationSets
     */
    public function showConfig(array $translationSets): void
    {
        foreach ($translationSets as $set) {
            $this->io->section('Translation-Set: ' . $set->getName());

            $this->io->writeln('-------------------------------------------------------------');
            $this->io->writeln(' Configuration for Translation-Set:');

            $caseStyles = $set->getCasingStyleSettings()->getCaseStyles();

            if (count($caseStyles) > 0) {
                $caseNames = [];
                foreach ($caseStyles as $caseStyle) {
                    $caseNames[] = $caseStyle->getName();
                }
                $styles = implode(', ', $caseNames);
            } else {
                $styles = 'none';
            }

            $this->io->writeln('   * Case-styles: ' . $styles);
            $this->io->writeln('-------------------------------------------------------------');
            $this->io->writeln('');
        }
    }
}
