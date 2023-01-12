<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFactory;
use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('import')
            ->setDescription('Imports translations from a provided exchange file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'R', ExchangeFormat::CSV)
            ->addOption('set', null, InputOption::VALUE_REQUIRED, 'R', '')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('csv-delimiter', null, InputOption::VALUE_REQUIRED, '', '');

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

        $io->title('PHPUnuhi Import');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $importFilename = (string)$input->getOption('file');
        $importExchangeFormat = (string)$input->getOption('format');
        $setName = (string)$input->getOption('set');

        # arguments for individual exchange exporters
        $delimiter = (string)$input->getOption('csv-delimiter');

        if (empty($delimiter)) {
            $delimiter = ',';
        }


        # adjust correct file path, required for PHAR loading
        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $importFilename = $workingDir . '/' . $importFilename;

        # -----------------------------------------------------------------

        if (empty($setName)) {
            throw new \Exception('Please provide a Translation-Set name that will be imported with argument --set=xyz');
        }


        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $result = null;

        foreach ($config->getTranslationSets() as $set) {

            if ($setName !== $set->getName()) {
                continue;
            }

            # get correct storage saver from our current set
            $storageSaver = StorageFactory::getStorage(
                $set->getFormat(),
                $set->getJsonIndent(),
                $set->isSortStorage()
            );

            # build the correct importer for our exchange format
            # and pass on the matching storage saver of our current ste
            $importer = ExchangeFactory::getImporterFromFormat($importExchangeFormat, $storageSaver, $delimiter);

            $result = $importer->import($set, $importFilename);
        }

        if ($result instanceof ImportResult) {
            $io->success('Imported ' . $result->getCountTranslations() . ' translations of ' . $result->getCountLocales() . ' locales for set: ' . $setName);
            exit(0);
        }

        $io->error('No sets found with name: ' . $setName);
        exit(1);
    }

} 
