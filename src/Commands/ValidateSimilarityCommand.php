<?php

declare(strict_types=1);

namespace PHPUnuhi\Commands;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;
use PHPUnuhi\Components\Reporter\Service\ReportTestResultConverter;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Facades\CLI\ReporterCliFacade;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Services\Similarity\Similarity;
use PHPUnuhi\Traits\CommandOutputTrait;
use PHPUnuhi\Traits\CommandTrait;
use PHPUnuhi\Traits\StringTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateSimilarityCommand extends Command
{
    use CommandTrait;
    use CommandOutputTrait;
    use StringTrait;


    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(CommandNames::VALIDATE_SIMILARITY)
            ->setDescription('Find similar or redundant translation keys.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('threshold', null, InputOption::VALUE_REQUIRED, 'The similarity threshold to use for matching keys. Similarities above the given threshold will lead to errors.', '90')
            ->addOption('report-format', null, InputOption::VALUE_REQUIRED, 'The report format for the generated similarity report', '')
            ->addOption('report-output', null, InputOption::VALUE_REQUIRED, 'The report output filename for the generated similarity report', '');

        parent::configure();
    }

    /**
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Validate Spelling');
        $this->showHeader();


        $configFile = $this->getConfigFile($input);
        $reportFormat = $this->getConfigStringValue('report-format', $input);
        $reportFilename = $this->getConfigStringValue('report-output', $input);
        $threshold = $this->getConfigFloatValue('threshold', $input, 90.0);

        # -----------------------------------------------------------------

        $io->writeln('Similarity threshold: ' . $threshold . '%' . PHP_EOL);

        $reporterCLI = new ReporterCliFacade($io);

        # -----------------------------------------------------------------
        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader(new XmlLoader());
        $config = $configLoader->load($configFile);

        $similarityService = new Similarity();


        $reportResult = new ReportResult();

        $reportConverter = new ReportTestResultConverter();

        $allErrors = [];
        $foundMainKeys = [];

        foreach ($config->getTranslationSets() as $translationSet) {
            $reportSetResult = new ReportSetResult($translationSet->getName());

            foreach ($translationSet->getLocales() as $locale) {
                $localeKeys = [];
                foreach ($locale->getTranslations() as $translation) {
                    $localeKeys[] = $translation->getKey();
                }

                $pairs = $similarityService->findSimilarString($localeKeys, $threshold);

                foreach ($pairs as $pair) {

                    # our similar entry was already found as main entry
                    # so we dont need it twice
                    if (in_array($pair['key2'], $foundMainKeys)) {
                        continue;
                    }

                    $foundMainKeys[] = $pair['key1'];

                    $test = $this->buildTestValidation($pair, $locale);

                    $reportTestResult = $reportConverter->toTestResult($test);
                    $reportSetResult->addTestResult($reportTestResult);

                    $allErrors[] = $test;
                }
            }

            $reportResult->addTranslationSet($reportSetResult);
        }


        $reporterCLI->execute($reportFormat, $reportFilename, $reportResult);

        # -----------------------------------------------------------------
        # -----------------------------------------------------------------

        $this->showErrorTable($allErrors, $io);

        $isValid = ($allErrors === []);

        if ($isValid) {
            $io->success('All translations are valid!');
            return 0;
        }

        $io->error('Found ' . count($allErrors) . ' similar keys!');
        return 1;
    }

    /**
     * @param string[] $pair
     */
    private function buildTestValidation(array $pair, Locale $locale): ValidationTest
    {
        $key1 = $pair['key1'];
        $key2 = $pair['key2'];
        $similarity = (float)$pair['similarity'];

        return new ValidationTest(
            $key1,
            $locale->getName(),
            'Test Similarity',
            '',
            0,
            'SIMILARITY',
            'Similar key: "' . $key2 . '" with similarity: ' . round($similarity, 2) . '%',
            false
        );
    }
}
