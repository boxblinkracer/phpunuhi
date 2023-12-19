<?php

namespace PHPUnuhi\Commands\Core;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Path\FileExtensionConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('migrate')
            ->setDescription('Migrates a storage type to another storage type.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, '', '');

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

        $io->title('PHPUnuhi Storage Migration');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $outputFormat = $this->getConfigStringValue('output', $input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);

        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation-Set: ' . $set->getName());

            $sourceStorage = StorageFactory::getInstance()->getStorage($set);
            $sourceStorage->loadTranslationSet($set);

            $targetStorage = StorageFactory::getInstance()->getStorageByFormat($outputFormat, $set);

            foreach ($set->getLocales() as $locale) {
                $filename = $this->getNewLocaleFilename($locale, $targetStorage);
                $targetStorage->saveTranslationLocale($locale, $filename);
            }
        }

        $io->section('Storage successfully migrated to format: ' . $outputFormat);

        exit(0);
    }

    /**
     * @param Locale $locale
     * @param StorageInterface $targetStorage
     * @return string
     */
    private function getNewLocaleFilename(Locale $locale, StorageInterface $targetStorage): string
    {
        $originalFilename = $locale->getFilename();

        $directory = pathinfo($originalFilename, PATHINFO_DIRNAME);
        $pureName = pathinfo($locale->getFilename(), PATHINFO_FILENAME);

        # also add ini section if existing
        $newFile = $directory . '/' . $pureName;

        if (!empty($locale->getIniSection())) {
            $newFile .= '_' . $locale->getIniSection();
        }

        return $newFile . ('.' . $targetStorage->getFileExtension());
    }

}
