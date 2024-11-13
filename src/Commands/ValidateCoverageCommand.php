<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Facades\CLI\CoverageCliFacade;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateCoverageCommand extends Command
{
    use CommandTrait;
    use StringTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(CommandNames::VALIDATE_COVERAGE)
            ->setDescription('Validates the coverage of the translations')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');
        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate Coverage');
        $this->showHeader();

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $coverageCLI = new CoverageCliFacade($io);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);

        $covSuccess = $coverageCLI->execute($config);

        # -----------------------------------------------------------------

        if ($covSuccess) {
            $io->success('Coverage checks passed!');
            return 0;
        }

        $io->error('Coverage checks failed');
        return 1;
    }
}
