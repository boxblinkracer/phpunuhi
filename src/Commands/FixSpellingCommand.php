<?php

namespace PHPUnuhi\Commands;


use PHPUnuhi\Bundles\Spelling\SpellCheckerFactory;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\TranslationNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixSpellingCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('fix:spelling')
            ->setDescription('Uses a capable translator service to fix the spelling of your translations.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '');

        $this->addTranslatorServiceOptions($this);

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

        $io->title('PHPUnuhi Fix Spelling');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $service = (string)$input->getOption('service');
        $setName = (string)$input->getOption('set');
        $deeplApiKey = (string)$input->getOption('deepl-key');
        $googleKey = (string)$input->getOption('google-key');
        $openAIKey = (string)$input->getOption('openai-key');

        $apiKey = $deeplApiKey;

        if (empty($apiKey)) {
            $apiKey = $googleKey;
        }

        if (empty($apiKey)) {
            $apiKey = $openAIKey;
        }

        # -----------------------------------------------------------------

        if (empty($service)) {
            throw new \Exception('No service provided for translation! Please set a service with argument --service=xyz');
        }

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $spellChecker = SpellCheckerFactory::fromService($service, $apiKey);


        $fixedCount = 0;
        $fixingFailedCount = 0;

        foreach ($config->getTranslationSets() as $set) {

            $translatedInSet = 0;

            # if we have configured to only translate a specific set then skip others
            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $allKeys = $set->getAllTranslationKeys();

            # first iterate through keys
            # then we have all keys next to each other for better comparing on CLI
            foreach ($allKeys as $currentKey) {

                foreach ($set->getLocales() as $locale) {

                    try {
                        $currentTranslation = $locale->findTranslation($currentKey);
                    } catch (TranslationNotFoundException $ex) {
                        # if no translation exits
                        # then skip this one
                        continue;
                    }

                    if ($currentTranslation->isEmpty()) {
                        continue;
                    }


                    $newValue = $spellChecker->fixSpelling(
                        $currentTranslation->getValue(),
                        $locale->getName()
                    );

                    if ($newValue === $currentTranslation->getValue()) {
                        $io->writeln('   [~] spelling of "' . $currentTranslation->getKey() . '" (' . $locale->getName() . ') is correct => ' . $newValue);
                        continue;
                    }

                    $io->writeln('          [+] fixed spelling of "' . $currentTranslation->getKey() . '" (' . $locale->getName() . ') => ' . $newValue);

                    if (!empty($newValue)) {
                        $fixedCount++;
                        $translatedInSet++;

                        $currentTranslation->setValue($newValue);
                    } else {
                        $fixingFailedCount++;
                    }

                }
            }

            if ($translatedInSet <= 0) {
                $io->note('nothing fixed in this set...');
                continue;
            }

            $io->block('saving translations of this set...');

            $storageSaver = StorageFactory::getStorage(
                $set->getFormat(),
                $set->getJsonIndent(),
                $set->isSortStorage()
            );

            $storageSaver->saveTranslations($set);
        }

        if ($fixingFailedCount > 0) {
            $io->warning($fixedCount . ' translation(s) are updated! ' . $fixingFailedCount . ' translation(s) not updated!');
            exit(0);
        }

        $io->success($fixedCount . ' translation(s) are updated!');
        exit(0);
    }

}
