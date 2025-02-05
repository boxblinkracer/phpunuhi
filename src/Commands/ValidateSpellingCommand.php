<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Spelling\SpellCheckerFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Facades\CLI\ReporterCliFacade;
use PHPUnuhi\Facades\CLI\SpellingValidatorCliFacade;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateSpellingCommand extends Command
{
    use CommandTrait;
    use StringTrait;


    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(CommandNames::VALIDATE_SPELLING)
            ->setDescription('Find misspelled translations.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'The spelling service you want to use', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '');

        foreach (SpellCheckerFactory::getInstance()->getAllOptions() as $option) {
            if ($option->hasValue()) {
                $this->addOption($option->getName(), null, InputOption::VALUE_REQUIRED, '');
            } else {
                $this->addOption($option->getName(), null, InputOption::VALUE_NONE, '');
            }
        }

        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate Spelling');
        $this->showHeader();

        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);

        # -----------------------------------------------------------------

        $service = $this->getConfigStringValue('service', $input);

        if ($service === '') {
            $service = (string)getenv(CommandEnvVariables::SPELLCHECKER_SERVICE);
        }

        $spellchecker = SpellCheckerFactory::getInstance()->fromService($service, $input->getOptions());

        $validatorCLI = new SpellingValidatorCliFacade($io, $spellchecker);
        $reporterCLI = new ReporterCliFacade($io);

        # -----------------------------------------------------------------
        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader(), $io);
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
