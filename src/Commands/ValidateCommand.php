<?php

namespace PHPUnuhi\Commands;


use PHPUnuhi\Components\Validator\Validator;
use PHPUnuhi\Configuration\ConfigurationLoader;
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
            ->setDescription('Validates all your translations from your configuration')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);

        $validator = new Validator();


        $isAllValid = true;

        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation Set: ' . $set->getName());

            $isValid = $validator->validate($set);

            if ($isValid) {
                $io->info('Set is valid!');
            } else {
                $io->info('Set is not valid!');
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
