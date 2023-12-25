<?php

namespace PHPUnuhi\Commands\Core;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\CaseStyle\CaseStyleConverterFactory;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixKeysCommand extends Command
{
    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('fix:keys')
            ->setDescription('Fixes the keys of your translation sets.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '');

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

        $io->title('PHPUnuhi Fix Keys');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $setName = $this->getConfigStringValue('set', $input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);


        $countCreated = 0;

        $converterFactory = new CaseStyleConverterFactory();

        foreach ($config->getTranslationSets() as $set) {
            if ($setName !== '' && $setName !== '0' && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Fixing Translation Set: ' . $set->getName());

            $storage = StorageFactory::getInstance()->getStorage($set);
            $delimiter = $storage->getHierarchy()->getDelimiter();

            foreach ($set->getLocales() as $locale) {
                foreach ($locale->getTranslations() as $translation) {
                    $translationNestingLevel = $translation->getLevel($delimiter);

                    $caseStyle = $set->getCasingStyle($translationNestingLevel);

                    if ($caseStyle === '') {
                        continue;
                    }

                    $converter = $converterFactory->fromIdentifier($caseStyle);

                    $io->writeln('   [' . $caseStyle . ', Lvl: ' . $translationNestingLevel . '] ' . $translation->getKey() . ' -> ' . $converter->convert($translation->getKey()));

                    $newKey = $converter->convert($translation->getKey());
                    $locale->updateTranslationKey($translation->getKey(), $newKey);
                }
            }

            $io->block('saving translations of this set...');

            $storageSaver = StorageFactory::getInstance()->getStorage($set);

            $storageSaver->saveTranslationSet($set);
        }

        $io->success($countCreated . ' translations have been created!');
        return 0;
    }
}
