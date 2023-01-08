<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatusCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Show the status and statistics of your translations')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'R', ExchangeFormat::CSV);

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

        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation Set: ' . $set->getName());

            $countMaxLocaleKeys = count($set->getAllTranslationKeys());
            $countAllKeys = $countMaxLocaleKeys * count($set->getLocales());

            $countAllFilled = 0;

            foreach ($set->getLocales() as $locale) {
                $filledList = $locale->findFilledTranslations();

                $countAllFilled += count($filledList);
            }

            $percent = round($countAllFilled / $countAllKeys, 2) * 100;

            $io->writeln("Coverage: " . ': ' . $percent . '% (' . $countAllFilled . '/' . $countAllKeys . ')');

            foreach ($set->getLocales() as $locale) {
                $countAllKeys = count($locale->getTranslationKeys());

                if ($countAllKeys === 0) {
                    $io->writeln("   [" . $locale->getName() . "] Coverage: 0% (0/" . $countMaxLocaleKeys . ")");

                } else {
                    $emptyList = $locale->findEmptyTranslations();
                    $filledList = $locale->findFilledTranslations();

                    $percent = round(count($filledList) / $countAllKeys, 2) * 100;

                    $io->writeln("   [" . $locale->getName() . '] Coverage: ' . $percent . ' % (' . count($filledList) . '/' . $countAllKeys . ')');
                }
            }
        }

        exit(0);
    }

}