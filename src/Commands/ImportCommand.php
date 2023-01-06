<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\CSV\CSVImporter;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Translation\JSON\JSONTranslationSaver;
use PHPUnuhi\Configuration\ConfigurationLoader;
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
            ->addOption('csv-delimiter', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('json-intent', null, InputOption::VALUE_OPTIONAL, '', '')
            ->addOption('json-sort', null, InputOption::VALUE_NONE, '', null);

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
        $importFilename = (string)$input->getOption('file');
        $suiteName = (string)$input->getOption('set');
        $intent = (string)$input->getOption('json-intent');
        $sort = (bool)$input->getOption('json-sort');
        $delimiter = (string)$input->getOption('csv-delimiter');


        if (empty($delimiter)) {
            $delimiter = ',';
        }

        if (empty($intent)) {
            $intent = 2;
        } else {
            $intent = (int)$intent;
        }

        $translationSaver = new JSONTranslationSaver($intent, $sort);

        $configLoader = ConfigurationLoader::fromFormat(Format::JSON);
        $config = $configLoader->load($configFile);


        # required for PHAR loading
        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $importFilename = $workingDir . '/' . $importFilename;


        $importer = new CSVImporter($translationSaver, $delimiter);


        $result = null;

        foreach ($config->getTranslationSets() as $set) {

            if ($suiteName !== $set->getName()) {
                continue;
            }

            $result = $importer->import($set, $importFilename);
        }

        if ($result instanceof ImportResult) {
            $io->success('Imported ' . $result->getCountTranslations() . ' translations of ' . $result->getCountLocales() . ' locales for set: ' . $suiteName);
            exit(0);
        }

        $io->warning('No sets found with name: ' . $suiteName);
        exit(1);
    }

} 
