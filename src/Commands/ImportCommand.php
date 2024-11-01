<?php

namespace PHPUnuhi\Commands;

use Exception;
use PHPUnuhi\Bundles\Exchange\ExchangeFactory;
use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Bundles\Exchange\ImportResult;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Components\Filter\FilterHandler;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
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
            ->setDescription('Imports translations from a provided exchange file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'R', ExchangeFormat::CSV)
            ->addOption('set', null, InputOption::VALUE_REQUIRED, 'R', '')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, '', '');

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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws ConfigurationException
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Import');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $importFilename = $this->getConfigStringValue('file', $input);
        $importExchangeFormat = $this->getConfigStringValue('format', $input);
        $setName = $this->getConfigStringValue('set', $input);

        # adjust correct file path, required for PHAR loading
        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $importFilename = $workingDir . '/' . $importFilename;

        # -----------------------------------------------------------------

        if ($setName === '' || $setName === '0') {
            throw new Exception('Please provide a Translation-Set name that will be imported with argument --set=xyz');
        }


        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);


        $result = null;

        $filterHandler = new FilterHandler();

        foreach ($config->getTranslationSets() as $set) {
            if ($setName !== $set->getName()) {
                continue;
            }

            # get correct storage saver from our current set
            $storageSaver = StorageFactory::getInstance()->getStorage($set);

            # build the correct importer for our exchange format
            # and pass on the matching storage saver of our current ste
            $importer = ExchangeFactory::getInstance()->getExchange($importExchangeFormat, $input->getOptions());

            # import our provided file
            $importData = $importer->import($importFilename);
            $this->updateTranslationSet($set, $importData);

            # filter away data
            $filterHandler->applyFilter($set);

            # save our data
            $result = $storageSaver->saveTranslationSet($set);
        }

        if ($result instanceof StorageSaveResult) {
            $io->success('Imported ' . $result->getSavedTranslations() . ' translations of ' . $result->getSavedLocales() . ' locales for set: ' . $setName);
            return 0;
        }

        $io->error('No sets found with name: ' . $setName);
        return 1;
    }

    /**
     * @param TranslationSet $set
     * @param ImportResult $importData
     * @return void
     */
    private function updateTranslationSet(TranslationSet $set, ImportResult $importData): void
    {
        foreach ($importData->getEntries() as $entry) {
            foreach ($set->getLocales() as $locale) {
                if ($entry->getLocaleExchangeID() !== $locale->getExchangeIdentifier()) {
                    continue;
                }

                foreach ($locale->getTranslations() as $translation) {
                    if ($translation->getKey() !== $entry->getKey()) {
                        continue;
                    }

                    if ($translation->getGroup() !== $entry->getGroup()) {
                        continue;
                    }

                    $translation->setValue($entry->getValue());
                }
            }
        }
    }
}
