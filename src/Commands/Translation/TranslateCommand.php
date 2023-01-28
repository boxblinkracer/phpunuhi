<?php

namespace PHPUnuhi\Commands\Translation;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Translator\TranslatorFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslateCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;


    /**
     * @var TranslatorFactory
     */
    private $translatorFactory;


    /**
     * @return void
     */
    protected function configure()
    {
        $this->translatorFactory = new TranslatorFactory();

        $this
            ->setName('translate')
            ->setDescription('Translate all your translations by using one of our translation services')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'The translator service you want to use', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('force', null, InputOption::VALUE_REQUIRED, 'a specific locale that you want to force to be translated', '');

        foreach ($this->translatorFactory->getAllOptions() as $option) {
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
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Translate');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        $service = $this->getConfigStringValue('service', $input);
        $setName = $this->getConfigStringValue('set', $input);
        $forceLocale = $this->getConfigStringValue('force', $input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $translator = $this->translatorFactory->fromService($service, $input->getOptions());

        # -----------------------------------------------------------------

        $translatedCount = 0;
        $translateFailedCount = 0;

        foreach ($config->getTranslationSets() as $set) {

            $translatedInSet = 0;

            # if we have configured to only translate a specific set then skip others
            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $allIDs = $set->getAllTranslationIDs();

            # first iterate through ids
            # then we have all ids next to each other for better comparing on CLI
            foreach ($allIDs as $currentID) {

                foreach ($set->getLocales() as $locale) {


                    # if we have configured to only translate a specific locale then skip other locales
                    if (!empty($forceLocale) && $forceLocale !== $locale->getName()) {
                        continue;
                    }

                    try {
                        $currentTranslation = $locale->findTranslation($currentID);
                    } catch (TranslationNotFoundException $ex) {
                        # if no translation exits
                        # then skip this one
                        continue;
                    }

                    # translate if we either force it or only if our value is empty
                    if ($forceLocale || $currentTranslation->isEmpty()) {

                        try {
                            $existingData = $set->findAnyExistingTranslation($currentID);
                        } catch (TranslationNotFoundException $ex) {
                            # if no translation exits
                            # then skip this one
                            continue;
                        }

                        $existingLocale = $existingData['locale'];
                        $existingTranslation = $existingData['translation'];

                        $newTranslation = $translator->translate(
                            $existingTranslation->getValue(),
                            $existingLocale,
                            $locale->getName()
                        );

                        $io->writeln('   [~] translating "' . $currentTranslation->getID() . '" (' . $locale->getName() . ') => ' . $newTranslation);

                        if (!empty($newTranslation)) {
                            $translatedCount++;
                            $translatedInSet++;

                            $currentTranslation->setValue($newTranslation);
                        } else {
                            $translateFailedCount++;
                        }
                    }
                }
            }

            if ($translatedInSet <= 0) {
                $io->note('nothing translated in this set...');
                continue;
            }

            $io->block('saving translations of this set...');

            $storageSaver = StorageFactory::getStorage($set);

            $storageSaver->saveTranslations($set);
        }

        if ($translateFailedCount > 0) {
            $io->warning($translatedCount . ' translation(s) are updated! ' . $translateFailedCount . ' translation(s) not updated!');
            exit(0);
        }

        $io->success($translatedCount . ' translation(s) are updated!');
        exit(0);
    }


}