<?php

namespace PHPUnuhi\Commands\Validation;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\SuiteResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\ReporterFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyleValidator;
use PHPUnuhi\Components\Validator\EmptyContentValidator;
use PHPUnuhi\Components\Validator\MissingStructureValidator;
use PHPUnuhi\Components\Validator\RulesValidator;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Coverage\CoverageService;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateAllCommand extends Command
{
    use CommandTrait;
    use StringTrait;

    private const COVERAGE_NOT_SET = 1;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('validate:all')
            ->setDescription('Validates everything')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '')
            ->addOption('min-coverage', null, InputOption::VALUE_REQUIRED, 'The minimum total translation coverage', '');

        parent::configure();
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CaseStyleNotFoundException
     * @throws ConfigurationException
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);
        $cliMinCoverage = $this->getConfigIntValue('min-coverage', $input, self::COVERAGE_NOT_SET);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);


        $isAllValid = true;

        $validators = [];

        $validators[] = new MissingStructureValidator();
        $validators[] = new CaseStyleValidator();
        $validators[] = new EmptyContentValidator();
        $validators[] = new RulesValidator();

        $errorCount = 0;

        $reportResult = new ReportResult();

        $coverageService = new CoverageService();


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
                        if (!empty($error->getLineNumber())) {
                            $io->writeln("   - Line: " . $error->getLineNumber());
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


            # -----------------------------------------------------------------
            $setCoverageResult = $coverageService->getCoverage([$set]);

            $validateMinCoverageValue = null;

            if ($cliMinCoverage > self::COVERAGE_NOT_SET) {
                $validateMinCoverageValue = $cliMinCoverage;
            } elseif ($set->hasMinCoverage()) {
                $validateMinCoverageValue = $set->getMinCoverage();
            }

            if ($validateMinCoverageValue !== null) {
                $covResult = $setCoverageResult->getCoverage();

                $io->writeln('Checking minimum coverage: ' . $validateMinCoverageValue . '%...');
                $io->writeln('   ...' . $covResult . '% of all translations in set "' . $set->getName() . '" are covered.');

                if ($covResult >= $validateMinCoverageValue) {
                    $io->writeln('   [/] PASSED: Minimum coverage of ' . $validateMinCoverageValue . '% in set "' . $set->getName() . '" is reached.');
                } else {
                    $isAllValid = false;
                    $io->writeln('   [x] FAILED: Minimum coverage of ' . $validateMinCoverageValue . '% in set "' . $set->getName() . '" is not reached.');
                }
            }
        }


        if ($reportFormat !== '') {
            $reporter = ReporterFactory::getInstance()->getReporter($reportFormat);

            if ($reportFilename === '') {
                $reportFilename = $reporter->getDefaultFilename();
            }

            $io->section('generating report...');

            $reporter->generate($reportFilename, $reportResult);

            $io->writeln('generated: ' . $reportFilename);
        }


        if ($isAllValid) {
            $io->success('All translations are valid!');
            return 0;
        }

        $io->error('Found ' . $errorCount . ' errors!');
        return 1;
    }
}
