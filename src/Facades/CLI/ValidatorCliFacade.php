<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\Model\TranslationSetResult;
use PHPUnuhi\Components\Validator\ValidatorInterface;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidatorCliFacade
{

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    /**
     * @param SymfonyStyle $io
     * @param ValidatorInterface[] $validators
     */
    public function __construct(SymfonyStyle $io, array $validators)
    {
        $this->io = $io;
        $this->validators = $validators;
    }

    /**
     * @param Configuration $config
     * @throws ConfigurationException
     * @return ReportResult
     */
    public function execute(Configuration $config): ReportResult
    {
        $reportResult = new ReportResult();

        $errorCount = 0;

        foreach ($config->getTranslationSets() as $set) {
            $this->io->section('Translation-Set: ' . $set->getName());

            $storage = StorageFactory::getInstance()->getStorage($set);


            $translationSetResult = new TranslationSetResult($set->getName());

            foreach ($this->validators as $validator) {
                $validatorResult = $validator->validate($set, $storage);

                if (!$validatorResult->isValid()) {
                    foreach ($validatorResult->getErrors() as $error) {
                        $errorCount++;

                        $this->io->writeln('#' . $errorCount . ' [' . $error->getClassification() . "] " . $error->getMessage());
                        $this->io->writeln('   - Locale: ' . $error->getLocale());

                        if (!empty($error->getFilename())) {
                            $this->io->writeln("   - File: " . $error->getFilename());
                        }

                        if (!empty($error->getLineNumber())) {
                            $this->io->writeln("   - Line: " . $error->getLineNumber());
                        }

                        $this->io->writeln('       [x]: ' . $error->getIdentifier());
                        $this->io->writeln('');
                    }
                }

                foreach ($validatorResult->getTests() as $test) {
                    $testResult = new TestResult(
                        $test->getTitle(),
                        $test->getTranslationKey(),
                        basename($test->getFilename()),
                        $test->getLineNumber(),
                        $test->getClassification(),
                        $test->getFailureMessage(),
                        $test->isSuccess()
                    );

                    $translationSetResult->addTestResult($testResult);
                }
            }

            $reportResult->addTranslationSet($translationSetResult);
        }

        return $reportResult;
    }
}
