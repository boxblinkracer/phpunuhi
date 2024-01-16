<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\Model\TranslationSetResult;
use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Style\SymfonyStyle;

class MessValidatorCliFacade
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
     * @return ReportResult
     */
    public function execute(array $translationSets): ReportResult
    {
        $reportResult = new ReportResult();

        foreach ($translationSets as $set) {
            $this->io->section('Translation-Set: ' . $set->getName());

            $setResult = new TranslationSetResult($set->getName());

            foreach ($set->getInvalidTranslationsIDs() as $translationID) {
                $setResult->addTestResult(
                    new TestResult(
                        $translationID,
                        $translationID,
                        '',
                        0,
                        'MESS',
                        'Not a single translation exists for this key. You might not need this translation.',
                        false
                    )
                );

                $this->io->writeln('   - Key: ' . $translationID);
                $this->io->writeln('       [x]: Not a single translation exists. You might not need this translation?!');
                $this->io->writeln('');
            }

            $reportResult->addTranslationSet($setResult);
        }

        return $reportResult;
    }
}
