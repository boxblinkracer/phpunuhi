<?php

namespace PHPUnuhi\Commands\Core;

use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListTranslationsCommand extends Command
{
    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('list:translations')
            ->setDescription('')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');

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

        $io->title('PHPUnuhi List Translations');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);


        foreach ($config->getTranslationSets() as $set) {
            $io->section('Translation Set: ' . $set->getName());

            foreach ($set->getAllTranslationIDs() as $id) {
                $io->writeln('   [~] ' . $id);
            }
        }

        return 0;
    }
}
