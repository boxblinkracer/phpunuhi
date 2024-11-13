<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Facades\CLI\MessValidatorCliFacade;
use PHPUnuhi\Facades\CLI\ReporterCliFacade;
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
            ->setName(CommandNames::VALIDATE_MESS)
            ->setDescription('Find messy translations that do not have a single value and might not be required anymore')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '');

        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate Mess');
        $this->showHeader();

        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);

        # -----------------------------------------------------------------

        $validatorCLI = new MessValidatorCliFacade($io);
        $reporterCLI = new ReporterCliFacade($io);

        # -----------------------------------------------------------------
        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);

        $validatorResult = $validatorCLI->execute($config->getTranslationSets());

        $reporterCLI->execute($reportFormat, $reportFilename, $validatorResult);

        # -----------------------------------------------------------------
        # -----------------------------------------------------------------

        $isValid = ($validatorResult->getFailureCount() === 0);

        if ($isValid) {
            $io->success('All translations are valid!');
            return 0;
        }

        $io->error('Found ' . $validatorResult->getFailureCount() . ' errors!');
        return 1;
    }
}
