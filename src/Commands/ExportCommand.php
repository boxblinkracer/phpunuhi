<?php

namespace SVRUnit\Commands;

use PHPUnuhi\Services\Configuration\ConfigurationLoader;
use SimpleXMLElement;
use SVRUnit\Commands\CommandTrait;
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


        foreach ($config->getTranslationSuites() as $suite) {

            $io->section('Translation Suite: ' . $suite->getName());

            $allEntries = [];

            foreach ($suite->getFiles() as $file) {

                $fileBase = basename($file);

                $snippetJson = (string)file_get_contents($file);
                $snippetArray = json_decode($snippetJson, true);

                if ($snippetArray === false) {
                    $snippetArray = [];
                }

                $snippetArrayFlat = $this->getFlatArray($snippetArray);

                foreach ($snippetArrayFlat as $key => $value) {
                    $allEntries[$key][$fileBase] = $value;
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

            $csvFilename = './export_' . $suite->getName() . '.csv';
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


    /**
     * @param array<mixed> $array
     * @param string $prefix
     * @return array<string>
     */
    private function getFlatArray(array $array, string $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->getFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }

}