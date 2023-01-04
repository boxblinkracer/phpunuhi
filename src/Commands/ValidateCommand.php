<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Commands\CommandTrait;
use PHPUnuhi\Services\Configuration\ConfigurationLoader;
use PHPUnuhi\Services\Validation\JsonValidator;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates all your translations in your configuration')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->showHeader();

        $configFile = $this->getConfigFile($input);

        $configLoader = new ConfigurationLoader();

        $config = $configLoader->load($configFile);


        $isAllValid = true;

        $validator = new JsonValidator();

        foreach ($config->getTranslationSuites() as $suite) {

            $io->section('Translation Suite: ' . $suite->getName());

            $isValid = $validator->validate($suite);

            if ($isValid) {
                $io->info('Suite is valid!');
            } else {
                $io->info('Suite is not valid!');
                $isAllValid = false;
            }
        }

        if ($isAllValid) {
            $io->success('All translations are valid!');
            exit(0);
        }

        $io->error('Translations are not valid!');
        exit(1);
    }

}