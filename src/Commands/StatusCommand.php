<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Components\Validator\Validator;
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

            $countKeys = count($set->getAllTranslationKeys()) * count($set->getLocales());

            $countSetEmpty = 0;
            $countSetFilled = 0;

            foreach ($set->getLocales() as $locale) {
                $emptyList = $locale->findEmptyTranslations();
                $filledList = $locale->findFilledTranslations();

                $countSetEmpty += count($emptyList);
                $countSetFilled += count($filledList);


            }

            $percent = round($countSetFilled / $countKeys, 2) * 100;

            $io->writeln("Coverage: " . ': ' . $percent . '% (' . $countSetFilled . '/' . $countKeys . ')');

            foreach ($set->getLocales() as $locale) {
                $countKeys = count($locale->getTranslationKeys());

                if ($countKeys === 0) {
                    $io->writeln("   [" . $locale->getName() . "] Coverage: 0,00% (0/0)");

                } else {
                    $emptyList = $locale->findEmptyTranslations();
                    $filledList = $locale->findFilledTranslations();

                    $percent = round(count($filledList) / $countKeys, 2) * 100;

                    $io->writeln("   [" . $locale->getName() . '] Coverage: ' . $percent . ' % (' . count($filledList) . ' / ' . $countKeys . ')');
                }
            

            }
        }

        exit(0);
    }

}