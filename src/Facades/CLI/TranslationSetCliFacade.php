<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslationSetCliFacade
{

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @param SymfonyStyle $io
     */
    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * @param TranslationSet[] $translationSets
     * @return void
     */
    public function showConfig(array $translationSets): void
    {
        foreach ($translationSets as $set) {
            $this->io->section('Translation-Set: ' . $set->getName());

            $this->io->writeln('-------------------------------------------------------------');
            $this->io->writeln(' Configuration for Translation-Set:');

            if (count($set->getCasingStyles()) > 0) {
                $caseNames = [];
                foreach ($set->getCasingStyles() as $caseStyle) {
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
