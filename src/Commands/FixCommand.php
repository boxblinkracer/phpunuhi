<?php

namespace PHPUnuhi\Commands;


use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('fix')
            ->setDescription('Fixes the structure of your translation sets')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $setName = (string)$input->getOption('set');

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $countCreated = 0;

        foreach ($config->getTranslationSets() as $set) {

            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Fixing Translation Set: ' . $set->getName());

            foreach ($set->getAllTranslationKeys() as $currentKey) {

                foreach ($set->getLocales() as $locale) {
                    try {
                        $locale->findTranslation($currentKey);
                    } catch (TranslationNotFoundException $ex) {
                        $io->writeln('   [+] create translation: [' . $locale->getName() . '] ' . $currentKey);
                        $locale->addTranslation($currentKey, '');
                        $countCreated++;
                    }
                }
            }

            $io->note('saving translations...');

            $storageSaver = StorageFactory::getSaverFromFormat(
                $set->getFormat(),
                $set->getJsonIntent(),
                $set->isJsonSort()
            );

            $storageSaver->saveTranslations($set);
        }

        $io->success($countCreated . ' translations have been created!');
        exit(0);
    }

}
