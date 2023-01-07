<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Translation\TranslatorFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslateCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('translate')
            ->setDescription('Translate all your translations by using one of our translation services')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('google-key', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('deepl-key', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('deepl-formal', null, InputOption::VALUE_NONE, '', null);

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

        $service = (string)$input->getOption('service');
        $setName = (string)$input->getOption('set');
        $deeplApiKey = (string)$input->getOption('deepl-key');
        $googleKey = (string)$input->getOption('google-key');
        $formal = (bool)$input->getOption('deepl-formal');

        $apiKey = $deeplApiKey;

        if (empty($deeplApiKey)) {
            $apiKey = $googleKey;
        }

        # -----------------------------------------------------------------

        if (empty($service)) {
            throw new \Exception('No service provided for translation! Please set a service with argument --service=xyz');
        }

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $translator = TranslatorFactory::fromService($service, $apiKey, $formal);


        $translatedCount = 0;
        $translateFailedCount = 0;

        foreach ($config->getTranslationSets() as $set) {

            # if we have configured to only export a single suite then skip all others
            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            foreach ($set->getLocales() as $locale) {
                foreach ($locale->getTranslations() as $currentTranslation) {

                    if (!empty(trim($currentTranslation->getValue()))) {
                        continue;
                    }

                    $existingData = $set->findAnyExistingTranslation($currentTranslation->getKey());

                    $existingLocale = $existingData['locale'];
                    $existingTranslation = $existingData['translation'];

                    $newTranslation = $translator->translate(
                        $existingTranslation->getValue(),
                        $existingLocale,
                        $locale->getName()
                    );

                    if (!empty($newTranslation)) {
                        $translatedCount++;

                        $currentTranslation->setValue($newTranslation);
                    } else {
                        $translateFailedCount++;
                    }

                }
            }

            $storageSaver = StorageFactory::getSaverFromFormat(
                $set->getFormat(),
                $set->getJsonIntent(),
                $set->isJsonSort()
            );

            $storageSaver->saveTranslations($set);
        }

        if ($translateFailedCount > 0) {
            $io->warning($translatedCount . ' translations are updated! ' . $translateFailedCount . ' translations not updated!');
            exit(0);
        }

        $io->success($translatedCount . ' translations are updated!');
        exit(0);
    }


}