<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\Model\TranslationSetResult;
use PHPUnuhi\Components\Validator\ValidatorInterface;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\TranslationSet;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidatorCliFacade
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
     * @param TranslationSet $set
     * @param ValidatorInterface[] $validators
     * @throws ConfigurationException
     * @return TranslationSetResult
     */
    public function execute(TranslationSet $set, array $validators): TranslationSetResult
    {
        $errorCount = 0;


        $this->io->section('Translation-Set: ' . $set->getName());

        $storage = StorageFactory::getInstance()->getStorage($set);


        $translationSetResult = new TranslationSetResult($set->getName());

        foreach ($validators as $validator) {
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

        return $translationSetResult;
    }
}
