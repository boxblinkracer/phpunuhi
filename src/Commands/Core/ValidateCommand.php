<?php

namespace PHPUnuhi\Commands\Core;

use PHPUnuhi\Commands\Validation\ValidateAllCommand;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
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
            ->setName('validate')
            ->setDescription('[DEPRECATED] Validates all your translations from your configuration. Please use validate:all instead.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for a generated report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated report', '')
            ->addOption('min-coverage', null, InputOption::VALUE_REQUIRED, 'The minimum total translation coverage', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ConfigurationException
     * @throws CaseStyleNotFoundException
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $originalCmd = new ValidateAllCommand();

        return $originalCmd->execute($input, $output);
    }
}
