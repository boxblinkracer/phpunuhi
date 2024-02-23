<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;
use PHPUnuhi\Components\Reporter\Service\ReportTestResultConverter;
use PHPUnuhi\Components\Validator\ValidatorInterface;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\CommandOutputTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidatorCliFacade
{
    use CommandOutputTrait;


    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param SymfonyStyle $io
     * @param OutputInterface $output
     */
    public function __construct(SymfonyStyle $io, OutputInterface $output)
    {
        $this->io = $io;
        $this->output = $output;
    }

    /**
     * @param TranslationSet $set
     * @param ValidatorInterface[] $validators
     * @throws ConfigurationException
     * @return ReportSetResult
     */
    public function execute(TranslationSet $set, array $validators): ReportSetResult
    {
        $this->io->section('Translation-Set: ' . $set->getName());

        $storage = StorageFactory::getInstance()->getStorage($set);


        $translationSetResult = new ReportSetResult($set->getName());

        $allTableErrors = [];
        $reporterConverter = new ReportTestResultConverter();

        foreach ($validators as $validator) {
            $validatorResult = $validator->validate($set, $storage);

            if (!$validatorResult->isValid()) {
                $allTableErrors = array_merge($allTableErrors, $validatorResult->getErrors());
            }

            foreach ($validatorResult->getTests() as $test) {
                $testResult = $reporterConverter->toTestResult($test);
                $translationSetResult->addTestResult($testResult);
            }
        }

        $this->showErrorTable($allTableErrors, $this->output);

        return $translationSetResult;
    }
}
