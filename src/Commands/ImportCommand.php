<?php

namespace PHPUnuhi\Commands;

use Configuration\ConfigurationLoader;
use PHPUnuhi\Models\Translation\Format;
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
            ->setDescription('Imports translations from a provided CSV file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, 'R', '')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('delimiter', null, InputOption::VALUE_REQUIRED, '', '')
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
        $suiteName = (string)$input->getOption('set');
        $intent = (string)$input->getOption('intent');
        $sort = (bool)$input->getOption('sort');
        $delimiter = (string)$input->getOption('delimiter');


        if (empty($delimiter)) {
            $delimiter = ',';
        }

        if (empty($intent)) {
            $intent = 2;
        } else {
            $intent = (int)$intent;
        }

        $configLoader = ConfigurationLoader::fromFormat(Format::JSON);
        $config = $configLoader->load($configFile);


        $translationFileValues = [];
        $headerFiles = [];


        # required for PHAR loading
        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $csvFilename = $workingDir . '/' . $csvFilename;


        $csvFile = fopen($csvFilename, 'r');

        if ($csvFile === false) {
            throw new \Exception('Error when opening CSV file: ' . $csvFilename);
        }

        while ($row = fgetcsv($csvFile, 0, $delimiter)) {

            if (count($headerFiles) === 0) {
                # header line
                $headerFiles = $row;
            } else {
                for ($i = 1; $i <= count($row) - 1; $i++) {
                    $key = $row[0];
                    $value = $row[$i];

                    $transFile = (string)$headerFiles[$i];

                    if ($transFile !== '') {
                        $translationFileValues[$transFile][$key] = $value;
                    }
                }
            }
        }

        fclose($csvFile);


        $importedLocales = 0;
        $importedTranslations = 0;

        foreach ($config->getTranslationSets() as $set) {

            if ($suiteName !== $set->getName()) {
                continue;
            }

            # todo only use 1 translation with arguments of this command!
            foreach ($set->getLocales() as $locale) {
                $fileName = basename($locale->getFilename());

                foreach ($translationFileValues as $key => $values) {

                    if ($key === $fileName) {

                        if ($sort) {
                            ksort($values);
                        }


                        $tmpArray = $this->flattenToMultiDimensional($values, '.');


                        $jsonString = (string)json_encode($tmpArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


                        $json = preg_replace_callback(
                            '/^ +/m',
                            function ($m) use ($intent) {
                                $intentStr = (string)str_repeat(' ', $intent);
                                $repeat = (int)(strlen($m[0]) / 2);
                                return str_repeat($intentStr, $repeat);
                            },
                            $jsonString
                        );

                        file_put_contents($locale->getFilename(), $json);

                        $importedLocales++;
                        $importedTranslations += count($locale->getTranslationKeys());
                    }
                }
            }
        }

        $io->success('Imported ' . $importedTranslations . ' translations of ' . $importedLocales . ' locales for set: ' . $suiteName);
        exit(0);

    }

    /**
     * @param array<mixed> $array
     * @param string $delimiter
     * @return array<mixed>
     */
    private function flattenToMultiDimensional(array $array, string $delimiter = '.'): array
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
