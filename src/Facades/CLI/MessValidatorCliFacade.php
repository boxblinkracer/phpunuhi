<?php

declare(strict_types=1);

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;
use PHPUnuhi\Components\Reporter\Service\ReportTestResultConverter;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\CommandOutputTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class MessValidatorCliFacade
{
    use CommandOutputTrait;

    private SymfonyStyle $io;


    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * @param TranslationSet[] $translationSets
     * @throws TranslationNotFoundException
     */
    public function execute(array $translationSets): ReportResult
    {
        $reportResult = new ReportResult();

        $allTests = [];

        $reportConverter = new ReportTestResultConverter();

        foreach ($translationSets as $set) {
            $this->io->section('Translation-Set: ' . $set->getName());

            $setResult = new ReportSetResult($set->getName());

            $invalidIDs = $set->getInvalidTranslationsIDs();

            foreach ($invalidIDs as $translationID) {
                $valTest = $this->buildTestValidation($translationID, false);
                $reportResultTest = $reportConverter->toTestResult($valTest);

                $allTests[] = $valTest;
                $setResult->addTestResult($reportResultTest);
            }

            foreach ($set->getAllTranslationIDs() as $translationID) {
                if (in_array($translationID, $invalidIDs)) {
                    continue;
                }

                $valTest = $this->buildTestValidation($translationID, true);
                $reportResultTest = $reportConverter->toTestResult($valTest);

                $allTests[] = $valTest;
                $setResult->addTestResult($reportResultTest);
            }

            $reportResult->addTranslationSet($setResult);
        }

        $this->showErrorTable($allTests, $this->io);

        return $reportResult;
    }


    private function buildTestValidation(string $translationID, bool $success): ValidationTest
    {
        return new ValidationTest(
            $translationID,
            '-',
            'Test if translation for key ' . $translationID . ' exists in any locale',
            '',
            0,
            'MESS',
            'Not a single translation exists for this key. You might not need this translation.',
            $success
        );
    }
}
