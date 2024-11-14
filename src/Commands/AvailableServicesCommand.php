<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFactory;
use PHPUnuhi\Bundles\Spelling\SpellCheckerFactory;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Translator\TranslatorFactory;
use PHPUnuhi\Bundles\Twig\ScannerFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AvailableServicesCommand extends Command
{
    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(CommandNames::AVAILABLE_SERVICES)
            ->setDescription('Show available services')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Services');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $configLoader->load($configFile);

        # -----------------------------------------------------------------

        $io->section('Storage Formats');

        foreach (StorageFactory::getInstance()->getStorages() as $service) {
            $io->writeln('    * ' . $service->getStorageName());
        }

        $io->section('Exchange Formats');

        foreach (ExchangeFactory::getInstance()->getExchangeFormats() as $service) {
            $io->writeln('    * ' . $service->getName());
        }

        $io->section('Translator Services');

        foreach (TranslatorFactory::getInstance()->getTranslators() as $service) {
            $io->writeln('    * ' . $service->getName());
        }

        $io->section('Spell Checker Services');

        foreach (SpellCheckerFactory::getInstance()->getSpellCheckers() as $service) {
            $io->writeln('    * ' . $service->getName());
        }

        $io->section('Scanner Services');

        foreach (ScannerFactory::getInstance()->getScanners() as $service) {
            $io->writeln('    * ' . $service->getScannerName());
        }

        return 0;
    }
}
