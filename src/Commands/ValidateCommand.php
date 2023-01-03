<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Services\Configuration\ConfigurationLoader;
use PHPUnuhi\Services\Validation\JsonValidator;
use SimpleXMLElement;
use SVRUnit\Commands\CommandTrait;
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
            ->setDescription('')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, 'Read configuration from XML file', '');

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

        $configFile = (string)$input->getOption('configuration');

        $configLoader = new ConfigurationLoader();

        $config = $configLoader->load($configFile);


        $isAllValid = true;

        $validator = new JsonValidator();

        foreach ($config->getTranslationSuites() as $suite) {

            $io->section('Translation Suite: ' . $suite->getName());

            $isValid = $validator->validate($suite->getFiles());

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