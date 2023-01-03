<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Services\Configuration\ConfigurationLoader;
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

        foreach ($config->getTranslationSuites() as $suite) {

            $io->section('Translation Suite: ' . $suite->getName());

            $isValid = $this->validateTranslation($suite->getFiles());

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

    /**
     * @param array $files
     * @return bool
     */
    private function validateTranslation(array $files): bool
    {
        $scopeSnippetCount = null;
        $foundSnippets = [];

        foreach ($files as $file) {


            $snippetJson = file_get_contents($file);
            $snippetArray = json_decode($snippetJson, true);

            $snippetArrayFlat = $this->getFlatArray($snippetArray);

            $allKeys = array_keys($snippetArrayFlat);
            $allValues = array_values($snippetArrayFlat);

            if ($scopeSnippetCount === null) {
                # its our first
                $scopeSnippetCount = count($allKeys);
            }

            foreach ($allKeys as $key) {
                $value = $snippetArrayFlat[$key];
                if (empty($value)) {
                    return false;
                }
            }

            foreach ($allKeys as $key) {
                $foundSnippets[$file][] = $key;
            }
        }


        # NOW COMPARE THAT THEY HAVE THE SAME STRUCTURE
        # ACROSS ALL FILES

        $previousFile = '';
        $previousKeys = null;
        foreach ($foundSnippets as $file => $snippetKeys) {

            if ($previousKeys !== null) {

                if (!$this->arrayEqual($previousKeys, $snippetKeys)) {

                    echo "Found difference in snippets in these files: " . PHP_EOL;
                    echo "  - A: " . $previousFile . PHP_EOL;
                    echo "  - B: " . $file . PHP_EOL;

                    $filtered = array_diff($previousKeys, $snippetKeys);
                    foreach ($filtered as $key) {
                        echo '           [x]: ' . $key . PHP_EOL;
                    }

                    return false;
                }
            }

            $previousFile = $file;
            $previousKeys = $snippetKeys;
        }

        return true;
    }


    /**
     * @param $array
     * @param $prefix
     * @return array
     */
    private function getFlatArray($array, $prefix = '')
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

    private function arrayEqual($a, $b)
    {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );


    }

}