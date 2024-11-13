<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Exceptions\ConfigurationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(CommandNames::VALIDATE)
            ->setDescription('[DEPRECATED] Validates all your translations from your configuration. Please use validate:all instead.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '')
            ->addOption('ignore-coverage', null, InputOption::VALUE_NONE, 'Ignore a configured coverage setting and proceed with strict validation.');

        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        return (new ValidateAllCommand())->execute($input, $output);
    }
}
