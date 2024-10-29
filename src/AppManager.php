<?php

namespace PHPUnuhi;

use Exception;
use PHPUnuhi\Commands\ExportCommand;
use PHPUnuhi\Commands\FixMessCommand;
use PHPUnuhi\Commands\FixSpellingCommand;
use PHPUnuhi\Commands\FixStructureCommand;
use PHPUnuhi\Commands\ImportCommand;
use PHPUnuhi\Commands\ListTranslationsCommand;
use PHPUnuhi\Commands\MigrateCommand;
use PHPUnuhi\Commands\ScanUsageCommand;
use PHPUnuhi\Commands\StatusCommand;
use PHPUnuhi\Commands\TranslateCommand;
use PHPUnuhi\Commands\ValidateAllCommand;
use PHPUnuhi\Commands\ValidateCommand;
use PHPUnuhi\Commands\ValidateCoverageCommand;
use PHPUnuhi\Commands\ValidateMessCommand;
use PHPUnuhi\Commands\ValidateSpellingCommand;
use PHPUnuhi\Commands\ValidateStructureCommand;
use Symfony\Component\Console\Application;

class AppManager
{

    /**
     * @throws Exception
     * @return void
     */
    public static function run(): void
    {
        $application = new Application('PHPUnuhi', PHPUnuhi::getVersion());

        $application->add(new StatusCommand());
        $application->add(new ListTranslationsCommand());

        $application->add(new ValidateCommand());
        $application->add(new ValidateAllCommand());
        $application->add(new ValidateMessCommand());
        $application->add(new ValidateCoverageCommand());
        $application->add(new ValidateStructureCommand());
        $application->add(new ValidateSpellingCommand());

        $application->add(new ImportCommand());
        $application->add(new ExportCommand());

        $application->add(new TranslateCommand());

        $application->add(new MigrateCommand());
        $application->add(new FixStructureCommand());
        $application->add(new FixMessCommand());

        $application->add(new ScanUsageCommand());

        $application->add(new FixSpellingCommand());

        $application->setDefaultCommand('list');

        $application->run();
    }
}
