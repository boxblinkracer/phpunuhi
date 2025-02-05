<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFactory;
use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{
    use CommandTrait;


    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('export')
            ->setDescription('Exports all or specific translations into an exchange file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('dir', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, '', ExchangeFormat::CSV)
            ->addOption('empty', null, InputOption::VALUE_NONE, '');

        foreach (ExchangeFactory::getInstance()->getAllOptions() as $option) {
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

        $io->title('PHPUnuhi Export');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $exportExchangeFormat = $this->getConfigStringValue('format', $input);
        $setName = $this->getConfigStringValue('set', $input);
        $outputDir = $this->getConfigStringValue('dir', $input);
        $onlyEmpty = $this->getConfigBoolValue('empty', $input);


        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $outputDir = $workingDir . '/' . $outputDir;

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader(), $io);

        $config = $configLoader->load($configFile);

        foreach ($config->getTranslationSets() as $set) {

            # if we have configured to only export a single suite then skip all others
            if ($setName !== '' && $setName !== '0' && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $exporter = ExchangeFactory::getInstance()->getExchange($exportExchangeFormat, $input->getOptions());

            $exporter->export($set, $outputDir, $onlyEmpty);
        }

        $io->success('All translations exported!');
        return 0;
    }
}
