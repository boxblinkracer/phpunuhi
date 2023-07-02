<?php

namespace PHPUnuhi\Commands\Translation;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Translator\TranslatorFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\PHPUnuhi;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;
use PHPUnuhi\Services\Placeholder\PlaceholderExtractor;
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
     * @var PlaceholderExtractor
     */
    private $placeholderExtractor;

    /**
     * @var PlaceholderEncoder
     */
    private $placeholderEncoder;


    /**
     * @return void
     */
    protected function configure()
    {
        $this->translatorFactory = new TranslatorFactory();
        $this->placeholderExtractor = new PlaceholderExtractor();
        $this->placeholderEncoder = new PlaceholderEncoder();

        $this
            ->setName('translate')
            ->setDescription('Translate all your translations by using one of our translation services')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'The translator service you want to use', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('force', null, InputOption::VALUE_REQUIRED, 'a specific locale that you want to force to be translated', '')
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'Optional name of the source locale to use for the translation', '');

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
        $sourceLocale = $this->getConfigStringValue('source', $input);

        $io->writeln("service: " . $service);
        $io->writeln("translation-set: " . $setName);
        $io->writeln("source (locale): " . $sourceLocale);
        $io->writeln("force (locale): " . $forceLocale);

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
                            $existingData = $set->findAnyExistingTranslation($currentID, $sourceLocale);
                        } catch (TranslationNotFoundException $ex) {
                            # if no translation exits then skip this one
                            if (!empty($sourceLocale)) {
                                $io->writeln('   [?] no existing translation found in locale ' . $sourceLocale . ' for key: ' . $currentTranslation->getID());
                            } else {
                                $io->writeln('   [?] no existing translation found in any of the locales for key: ' . $currentTranslation->getID());
                            }
                            continue;
                        }

                        $existingLocale = $existingData['locale'];
                        $existingTranslation = $existingData['translation'];
                        $existingValue = $existingTranslation->getValue();

                        $foundPlaceholders = [];

                        # -----------------------------------------------------------------------------------------------------------------------------------

                        foreach ($set->getProtection()->getMarkers() as $marker) {
                            # search for all possible placeholders that exist, like %productName%
                            # we must not translate them (happens with DeepL, ...)
                            $markerPlaceholders = $this->placeholderExtractor->extract(
                                $existingValue,
                                $marker->getStart(),
                                $marker->getEnd()
                            );

                            $foundPlaceholders = array_merge($foundPlaceholders, $markerPlaceholders);
                        }

                        foreach ($set->getProtection()->getTerms() as $term) {
                            # just add these as placeholders
                            $foundPlaceholders[] = new Placeholder($term);
                        }

                        # encode the value to have something that won't get translated like //12//
                        $existingValue = $this->placeholderEncoder->encode($existingValue, $foundPlaceholders);

                        # start our third party translation service
                        $newTranslation = $translator->translate(
                            $existingValue,
                            $existingLocale,
                            $locale->getName()
                        );

                        if (count($foundPlaceholders) > 0) {
                            # decode our string so that we have the original placeholder values again (%productName%)
                            $newTranslation = $this->placeholderEncoder->decode($newTranslation, $foundPlaceholders);
                        }

                        # -----------------------------------------------------------------------------------------------------------------------------------

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

            $storageSaver->saveTranslationSet($set);
        }

        if ($translateFailedCount > 0) {
            $io->warning($translatedCount . ' translation(s) are updated! ' . $translateFailedCount . ' translation(s) not updated!');
            exit(0);
        }

        $io->success($translatedCount . ' translation(s) are updated!');
        exit(0);
    }


}