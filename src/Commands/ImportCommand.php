<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Services\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('import')
            ->setDescription('')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, 'Read configuration from XML file', '')
            ->addOption('suite', null, InputOption::VALUE_REQUIRED, 'R', '')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('intent', null, InputOption::VALUE_OPTIONAL, '', '')
            ->addOption('sort', null, InputOption::VALUE_NONE, '', null);

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
        $csvFilename = (string)$input->getOption('file');
        $suiteName = (string)$input->getOption('suite');
        $intent = (string)$input->getOption('intent');
        $sort = (bool)$input->getOption('sort');


        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);

        $csvFilename = realpath(dirname($csvFilename)) . '/' . basename($csvFilename);

        if (empty($intent)) {
            $intent = 2;
        } else {
            $intent = (int)$intent;
        }

        /** @var array<mixed> $csvFile */
        $csvFile = file($csvFilename);

        $translationFileValues = [];
        $headerFiles = [];
        foreach ($csvFile as $line) {

            $lineArray = str_getcsv($line);

            if (count($headerFiles) === 0) {
                # header line
                $headerFiles = $lineArray;
            } else {
                for ($i = 1; $i <= count($lineArray) - 1; $i++) {
                    $key = $lineArray[0];
                    $value = $lineArray[$i];

                    $transFile = (string)$headerFiles[$i];

                    if ($transFile !== '') {
                        $translationFileValues[$transFile][$key] = $value;
                    }

                }
            }
        }

        $importedLocales = 0;
        $importedTranslations = 0;

        foreach ($config->getTranslationSuites() as $suite) {

            if ($suiteName !== $suite->getName()) {
                continue;
            }

            # todo only use 1 translation with arguments of this command!
            foreach ($suite->getLocales() as $locale) {
                $fileName = basename($locale->getFilename());

                foreach ($translationFileValues as $key => $values) {

                    if ($key === $fileName) {

                        if ($sort) {
                            ksort($values);
                        }

                        $tmpArray = $this->flattenToMultiDimensional($values, '.');

                        $json = preg_replace_callback(
                            '/^ +/m',
                            function ($m) use ($intent) {
                                $intentStr = str_repeat(' ', $intent);
                                return str_repeat($intentStr, strlen($m[0]) / 2);
                            },
                            json_encode($tmpArray, JSON_PRETTY_PRINT)
                        );

                        file_put_contents($locale->getFilename(), $json);

                        $importedLocales++;
                        $importedTranslations += count($locale->getTranslationKeys());
                    }
                }
            }
        }

        $io->success('Imported ' . $importedTranslations . ' translations of ' . $importedLocales . ' locales for suite: ' . $suiteName);
        exit(0);

    }

    /**
     * @param array<mixed> $array
     * @param string $delimiter
     * @return array<mixed>
     */
    private function flattenToMultiDimensional(array $array, string $delimiter = '.')
    {
        $result = [];
        foreach ($array as $notations => $value) {
            // extract keys
            $keys = explode($delimiter, $notations);

            if ($keys === false) {
                $keys = [];
            }

            // reverse keys for assignments
            $keys = array_reverse($keys);


            // set initial value
            $lastVal = $value;
            foreach ($keys as $key) {
                // wrap value with key over each iteration
                $lastVal = [
                    $key => $lastVal
                ];
            }

            // merge result
            $result = array_merge_recursive($result, $lastVal);
        }

        return $result;
    }

}
