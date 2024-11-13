<?php

declare(strict_types=1);

namespace PHPUnuhi\Facades\CLI;

use PHPUnuhi\Components\Validator\Coverage\CoverageValidator;
use PHPUnuhi\Models\Configuration\Configuration;
use Symfony\Component\Console\Style\SymfonyStyle;

class CoverageCliFacade
{
    private SymfonyStyle $io;

    private CoverageValidator $coverageValidator;



    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;

        $this->coverageValidator = new CoverageValidator();
    }


    public function execute(Configuration $config): bool
    {
        $this->io->section('coverage checks');
        $this->io->writeln('Checking minimum coverage...');

        $rootCovConfig = $config->getCoverage();
        $translationSets = $config->getTranslationSets();

        $covResult = $this->coverageValidator->validate($rootCovConfig, $translationSets);

        if ($covResult->isSuccess()) {
            $this->io->writeln('   [/] PASSED: Minimum coverage is reached.');
            return true;
        }

        $this->io->writeln('   [x] FAILED: Coverage not reached for: ' . $covResult->getScope());
        $this->io->writeln('               Expected ' . $covResult->getCoverageExpected() . '%, actual ' . $covResult->getCoverageActual() . '%');

        return false;
    }
}
