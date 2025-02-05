<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Coverage\CoverageService;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Traits\CommandTrait;
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
            ->setName(CommandNames::STATUS)
            ->setDescription('Show the status and statistics of your translations')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'R', ExchangeFormat::CSV);

        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Status');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader(), $io);
        $config = $configLoader->load($configFile);


        $coverageService = new CoverageService();

        $coverageResult = $coverageService->getCoverage($config->getTranslationSets());

        foreach ($coverageResult->getTranslationSetCoverages() as $translationSet) {
            $io->section('Translation Set: ' . $translationSet->getName());

            $io->writeln("Coverage: " . $translationSet->getCoverage() . '% (' . $translationSet->getCountTranslated() . '/' . $translationSet->getCountAll() . ')');

            foreach ($translationSet->getLocaleCoverages() as $localeCoverage) {
                $io->writeln("   [" . $localeCoverage->getLocaleName() . '] Coverage: ' . $localeCoverage->getCoverage() . '% (' . $localeCoverage->getCountTranslated() . '/' . $localeCoverage->getCountAll() . ')');
            }

            $io->writeln('');
            $io->writeln("Words: " . $translationSet->getWordCount());

            foreach ($translationSet->getLocaleCoverages() as $localeCoverage) {
                $io->writeln("   [" . $localeCoverage->getLocaleName() . '] Words: ' . $localeCoverage->getWordCount());
            }
        }

        $io->section('Total Sets [' . count($config->getTranslationSets()) . ']');

        $io->writeln('   Coverage: ' . $coverageResult->getCoverage() . '% (' . $coverageResult->getCountTranslated() . '/' . $coverageResult->getCountAll() . ')');
        $io->writeln('   Words: ' . $coverageResult->getWordCount());

        return 0;
    }
}
