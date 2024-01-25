<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Validator\MissingStructureValidator;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Facades\CLI\ReporterCliFacade;
use PHPUnuhi\Facades\CLI\TranslationSetCliFacade;
use PHPUnuhi\Facades\CLI\ValidatorCliFacade;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateStructureCommand extends Command
{
    use CommandTrait;
    use StringTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(CommandNames::VALIDATE_STRUCTURE)
            ->setDescription('Validates the structure')
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

        $io->title('PHPUnuhi Validate Structure');
        $this->showHeader();

        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);

        # -----------------------------------------------------------------

        $validators = [];
        $validators[] = new MissingStructureValidator();

        $translationSetCLI = new TranslationSetCliFacade($io);
        $validatorsCLI = new ValidatorCliFacade($io);
        $reporterCLI = new ReporterCliFacade($io);

        # -----------------------------------------------------------------
        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);

        $translationSetCLI->showConfig($config->getTranslationSets());

        $validatorsResult = new ReportResult();

        foreach ($config->getTranslationSets() as $set) {
            $result = $validatorsCLI->execute($set, $validators);

            $validatorsResult->addTranslationSet($result);
        }

        $reporterCLI->execute($reportFormat, $reportFilename, $validatorsResult);

        # -----------------------------------------------------------------

        $isAllValid = ($validatorsResult->getFailureCount() === 0);


        if ($isAllValid) {
            if ($validatorsResult->getFailureCount() > 0) {
                $io->warning('Validation successful, but found ' . $validatorsResult->getFailureCount() . ' warnings!');
            } else {
                $io->success('All translations are valid!');
            }

            return 0;
        }

        $io->error('Found ' . $validatorsResult->getFailureCount() . ' errors!');
        return 1;
    }
}
