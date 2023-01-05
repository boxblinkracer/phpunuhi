<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Export\CSV\CSVExporter;
use PHPUnuhi\Export\HTML\HTMLExporter;
use PHPUnuhi\Models\Translation\Format;
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
            ->setDescription('Exports all or specific translations into a CSV file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('dir', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('delimiter', null, InputOption::VALUE_REQUIRED, '', '');

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
        $outputDir = (string)$input->getOption('dir');
        $setName = (string)$input->getOption('set');
        $delimiter = (string)$input->getOption('delimiter');

        if (empty($delimiter)) {
            $delimiter = ',';
        }


        $configLoader = ConfigurationLoader::fromFormat(Format::JSON);

        $config = $configLoader->load($configFile);


        $exporter = new CSVExporter($delimiter);

        foreach ($config->getTranslationSets() as $set) {

            # if we have configured to only export a single suite
            # then skip all others
            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $exporter->export($set, $outputDir);
        }

        $io->success('All translations exported!');
        exit(0);
    }

}
