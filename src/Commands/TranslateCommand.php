<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Translator\TranslatorFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderExtractor;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslateCommand extends Command
{
    use CommandTrait;

    public const ENV_TRANSLATION_SERVICE = 'TRANSLATION_SERVICE';

    /**
     * @var PlaceholderExtractor
     */
    private $placeholderExtractor;


    /**
     * @return void
     */
    protected function configure()
    {
        $this->placeholderExtractor = new PlaceholderExtractor();

        $this
            ->setName('translate')
            ->setDescription('Translate all your translations by using one of our translation services')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'The translator service you want to use', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('force', null, InputOption::VALUE_REQUIRED, 'a specific locale that you want to force to be translated', '')
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'Optional name of the source locale to use for the translation', '');

        foreach (TranslatorFactory::getInstance()->getAllOptions() as $option) {
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

        $io->title('PHPUnuhi Translate');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        $service = $this->getConfigStringValue('service', $input);
        if ($service === '' || $service === '0') {
            $service = (string) getenv(self::ENV_TRANSLATION_SERVICE);
        }

        $setName = $this->getConfigStringValue('set', $input);
        $forceLocale = $this->getConfigStringValue('force', $input);
        $sourceLocale = $this->getConfigStringValue('source', $input);

        $io->writeln("service: " . $service);
        $io->writeln("translation-set: " . $setName);
        $io->writeln("source (locale): " . $sourceLocale);
        $io->writeln("force (locale): " . $forceLocale);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);


        $translator = TranslatorFactory::getInstance()->fromService($service, $input->getOptions());

        # -----------------------------------------------------------------

        $translatedCount = 0;
        $translateFailedCount = 0;

        foreach ($config->getTranslationSets() as $set) {
            $translatedInSet = 0;

            # if we have configured to only translate a specific set then skip others
            if ($setName !== '' && $setName !== '0' && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $allIDs = $set->getAllTranslationIDs();

            # first iterate through ids
            # then we have all ids next to each other for better comparing on CLI
            foreach ($allIDs as $currentID) {
                foreach ($set->getLocales() as $locale) {

                    # if we have configured to only translate a specific locale then skip other locales
                    if ($forceLocale !== '' && $forceLocale !== '0' && $forceLocale !== $locale->getName()) {
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
                            if ($sourceLocale !== '' && $sourceLocale !== '0') {
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

                        # start our third party translation service
                        $newTranslation = $translator->translate(
                            $existingValue,
                            $existingLocale,
                            $locale->getName(),
                            $foundPlaceholders
                        );

                        # -----------------------------------------------------------------------------------------------------------------------------------

                        $io->writeln('   [~] translating "' . $currentTranslation->getID() . '" (' . $locale->getName() . ') => ' . $newTranslation);

                        if ($newTranslation !== '' && $newTranslation !== '0') {
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

            $storageSaver = StorageFactory::getInstance()->getStorage($set);

            $storageSaver->saveTranslationSet($set);
        }

        if ($translateFailedCount > 0) {
            $io->warning($translatedCount . ' translation(s) are updated! ' . $translateFailedCount . ' translation(s) not updated!');
            return 0;
        }

        $io->success($translatedCount . ' translation(s) are updated!');
        return 0;
    }
}
