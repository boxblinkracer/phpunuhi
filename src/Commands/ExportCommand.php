<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Services\Configuration\ConfigurationLoader;
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
            ->addOption('dir', null, InputOption::VALUE_OPTIONAL, '', '');

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


        $configLoader = new ConfigurationLoader();

        $config = $configLoader->load($configFile);


        foreach ($config->getTranslationSets() as $set) {

            # if we have configured to only export a single suite
            # then skip all others
            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $allEntries = [];

            foreach ($set->getLocales() as $locale) {

                $fileBase = basename($locale->getFilename());

                foreach ($locale->getTranslations() as $translation) {
                    $allEntries[$translation->getKey()][$fileBase] = $translation->getValue();
                }
            }


            $lines = [];

            foreach ($allEntries as $key => $values) {

                $line = [];
                $line[] = 'Key';

                foreach ($values as $file => $value) {
                    $line[] = $file;
                }

                $lines[] = $line;
                break;
            }

            foreach ($allEntries as $key => $values) {

                $line = [];
                $line[] = $key;
                foreach ($values as $value) {
                    $line[] = $value;
                }

                $lines[] = $line;
            }

            if (empty($outputDir)) {
                $outputDir = '.';
            } else {
                if (!file_exists($outputDir)) {
                    mkdir($outputDir);
                }
            }

            $csvFilename = $outputDir . '/' . $set->getName() . '.csv';

            if (file_exists($csvFilename)) {
                unlink($csvFilename);
            }

            $f = fopen($csvFilename, 'a');

            if ($f !== false) {
                foreach ($lines as $row) {
                    fputcsv($f, $row);
                }
                fclose($f);
            }

        }

        $io->success('All translations exported!');
        exit(0);
    }

}
