<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Services\Maths\PercentageCalculator;
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

        $io->title('PHPUnuhi Status');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);

        $calculator = new PercentageCalculator();


        $totalTranslations = 0;
        $totalValidTranslations = 0;

        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation Set: ' . $set->getName());

            $countMaxLocaleKeys = count($set->getAllTranslationKeys());
            $countSetKeys = $countMaxLocaleKeys * count($set->getLocales());

            $totalTranslations += $countSetKeys;

            $countSetValid = 0;

            foreach ($set->getLocales() as $locale) {
                $countSetValid += count($locale->getValidTranslations());
            }

            $totalValidTranslations += $countSetValid;

            $percent = $calculator->getRoundedPercentage($countSetValid, $countSetKeys);

            $io->writeln("Coverage: " . $percent . '% (' . $countSetValid . '/' . $countSetKeys . ')');


            foreach ($set->getLocales() as $locale) {
                $countLocaleKeys = count($locale->getTranslationKeys());

                if ($countLocaleKeys === 0) {
                    # if our current locale has no keys
                    # then show 0,00 %, and also show the maximum number of keys as coverage possibility
                    $io->writeln("   [" . $locale->getName() . "] Coverage: 0% (0/" . $countMaxLocaleKeys . ")");
                    continue;
                }

                $countLocaleValid = count($locale->getValidTranslations());

                $percent = $calculator->getRoundedPercentage($countLocaleValid, $countLocaleKeys);

                $io->writeln("   [" . $locale->getName() . '] Coverage: ' . $percent . '% (' . $countLocaleValid . '/' . $countLocaleKeys . ')');
            }
        }

        $io->section('Total Sets [' . count($config->getTranslationSets()) . ']');

        $percent = $calculator->getRoundedPercentage($totalValidTranslations, $totalTranslations);

        $io->writeln('   Coverage: ' . $percent . '% (' . $totalValidTranslations . '/' . $totalTranslations . ')');

        exit(0);
    }

}