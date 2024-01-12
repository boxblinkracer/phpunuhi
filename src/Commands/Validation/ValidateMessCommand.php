<?php

namespace PHPUnuhi\Commands\Validation;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\SuiteResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\ReporterFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateMessCommand extends Command
{
    use CommandTrait;
    use StringTrait;


    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('validate:mess')
            ->setDescription('Find messy translations that do not have a single value and might not be required anymore')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ConfigurationException
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate Mess');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);


        $isAllValid = true;


        $errorCount = 0;

        $reportResult = new ReportResult();

        foreach ($config->getTranslationSets() as $set) {
            $io->section('Translation-Set: ' . $set->getName());

            $setResult = new SuiteResult($set->getName());

            foreach ($set->getInvalidTranslationsIDs() as $translationID) {
                $errorCount++;
                $isAllValid = false;

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

                $io->writeln('   - Key: ' . $translationID);
                $io->writeln('       [x]: Not a single translation exists. You might not need this translation?!');
                $io->writeln('');
            }

            $reportResult->addSuite($setResult);
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
