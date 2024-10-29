<?php

namespace PHPUnuhi\Commands;

use Exception;
use PHPUnuhi\Bundles\Spelling\SpellCheckerFactory;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Models\Text\Text;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixSpellingCommand extends Command
{
    use CommandTrait;

    public const ENV_SPELLCHECKER_SERVICE = 'SPELLCHECKER_SERVICE';

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('fix:spelling')
            ->setDescription('Uses a capable translator service to fix the spelling of your translations.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'The spelling service you want to use', '');

        foreach (SpellCheckerFactory::getInstance()->getAllOptions() as $option) {
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
     * @throws Exception
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Fix Spelling');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        $service = $this->getConfigStringValue('service', $input);

        if ($service === '') {
            $service = (string)getenv(self::ENV_SPELLCHECKER_SERVICE);
        }

        $io->writeln("service: " . $service);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);

        $spellChecker = SpellCheckerFactory::getInstance()->fromService($service, $input->getOptions());


        $countFixed = 0;

        foreach ($config->getTranslationSets() as $set) {
            $io->section('Fixing spelling Translation Set: ' . $set->getName());

            foreach ($set->getLocales() as $locale) {
                foreach ($locale->getValidTranslations() as $translation) {

                    # in case of placeholders, get the encoded text
                    $encodedText = $set->getEncodedValue($translation->getValue());

                    # fix our spelling
                    $fixed = $spellChecker->fixSpelling(
                        new Text($translation->getValue(), $encodedText),
                        $locale->getName()
                    );

                    # decode the text back, so it contains placeholders again
                    $fixed = $set->getDecodedText($translation->getValue(), $fixed);

                    if ($fixed !== $translation->getValue()) {
                        $io->writeln('   [+] Fixed "' . $translation->getID() . '" (' . $locale->getName() . '): ' . $translation->getValue() . ' => ' . $fixed);

                        $translation->setValue($fixed);
                        $countFixed++;
                    } else {
                        $io->writeln('   [~] Spelling already correct: "' . $translation->getID() . '" (' . $locale->getName() . ') => ' . $translation->getValue());
                    }
                }
            }

            $storageSaver = StorageFactory::getInstance()->getStorage($set);

            $storageSaver->saveTranslationSet($set);
        }

        $io->success($countFixed . ' spellings have been fixed!');
        return 0;
    }
}
