<?php

namespace PHPUnuhi\Commands\Core;


use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Reporter\JUnit\JUnitReporter;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\SuiteResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\ReporterFactory;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Components\Validator\MissingStructureValidator;
use PHPUnuhi\Components\Validator\Rules\DisallowedTextsRule;
use PHPUnuhi\Components\Validator\Rules\MaxKeyLengthRule;
use PHPUnuhi\Components\Validator\Rules\NestingDepthRule;
use PHPUnuhi\Components\Validator\RulesValidator;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateCommand extends Command
{

    use CommandTrait;
    use StringTrait;


    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates all your translations from your configuration')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $isAllValid = true;

        $validators = [];

        $validators[] = new MissingStructureValidator();
        $validators[] = new CaseStyleValidator();
        $validators[] = new EmptyContentValidator();
        $validators[] = new RulesValidator();

        $errorCount = 0;

        $reportResult = new ReportResult();

        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation-Set: ' . $set->getName());

            $io->writeln('-------------------------------------------------------------');
            $io->writeln(' Configuration for Translation-Set:');

            if (count($set->getCasingStyles()) > 0) {
                $caseNames = [];
                foreach ($set->getCasingStyles() as $caseStyle) {
                    $caseNames[] = $caseStyle->getName();
                }
                $styles = implode(', ', $caseNames);
            } else {
                $styles = 'none';
            }

            $io->writeln('   * Case-styles: ' . $styles);
            $io->writeln('-------------------------------------------------------------');
            $io->writeln('');

            $storage = StorageFactory::getInstance()->getStorage($set);


            $suiteResult = new SuiteResult($set->getName());

            foreach ($validators as $validator) {

                $result = $validator->validate($set, $storage);

                if (!$result->isValid()) {
                    $isAllValid = false;

                    foreach ($result->getErrors() as $error) {
                        $errorCount++;

                        $io->writeln('#' . $errorCount . ' [' . $error->getClassification() . "] " . $error->getMessage());
                        $io->writeln('   - Locale: ' . $error->getLocale());
                        if (!empty($error->getFilename())) {
                            $io->writeln("   - File: " . $error->getFilename());
                        }
                        $io->writeln('       [x]: ' . $error->getIdentifier());
                        $io->writeln('');
                    }
                }

                foreach ($result->getTests() as $test) {
                    $testResult = new TestResult(
                        $test->getTitle(),
                        $test->getTranslationKey(),
                        basename($test->getFilename()),
                        $test->getLineNumber(),
                        $test->getClassification(),
                        $test->getFailureMessage(),
                        $test->isSuccess()
                    );

                    $suiteResult->addTestResult($testResult);
                }
            }

            $reportResult->addSuite($suiteResult);
        }


        if (!empty($reportFormat)) {

            $reporter = ReporterFactory::getInstance()->getReporter($reportFormat);

            if (empty($reportFilename)) {
                $reportFilename = $reporter->getDefaultFilename();
            }

            $io->section('generating report...');

            $reporter->generate($reportFilename, $reportResult);

            $io->writeln('generated: ' . $reportFilename);
        }


        if ($isAllValid) {
            $io->success('All translations are valid!');
            exit(0);
        }

        $io->error('Found ' . $errorCount . ' errors!');
        exit(1);
    }

}
