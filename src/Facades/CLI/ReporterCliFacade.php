<?php

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\ReporterFactory;
use PHPUnuhi\Exceptions\ConfigurationException;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReporterCliFacade
{

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @param SymfonyStyle $io
     */
    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }


    /**
     * @param string $reportFormat
     * @param string $reportFilename
     * @param ReportResult $reportResult
     * @throws ConfigurationException
     * @return void
     */
    public function execute(string $reportFormat, string $reportFilename, ReportResult $reportResult): void
    {
        if ($reportFormat !== '') {
            $reporter = ReporterFactory::getInstance()->getReporter($reportFormat);

            if ($reportFilename === '') {
                $reportFilename = $reporter->getDefaultFilename();
            }

            $this->io->section('generating report...');

            $reporter->generate($reportFilename, $reportResult);

            $this->io->writeln('generated: ' . $reportFilename);
        }
    }
}
