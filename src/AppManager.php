<?php

namespace PHPUnuhi;

use Exception;
use PHPUnuhi\Commands\Core\FixStructureCommand;
use PHPUnuhi\Commands\Core\ListTranslationsCommand;
use PHPUnuhi\Commands\Core\MigrateCommand;
use PHPUnuhi\Commands\Core\StatusCommand;
use PHPUnuhi\Commands\Core\ValidateCommand;
use PHPUnuhi\Commands\Exchange\ExportCommand;
use PHPUnuhi\Commands\Exchange\ImportCommand;
use PHPUnuhi\Commands\Translation\TranslateCommand;
use Symfony\Component\Console\Application;

class AppManager
{

    /**
     * @param array<mixed> $arguments
     * @return void
     * @throws Exception
     */
    public static function run(array $arguments)
    {
        $application = new Application('PHPUnuhi', PHPUnuhi::getVersion());

        $application->add(new StatusCommand());
        $application->add(new ValidateCommand());
        $application->add(new ExportCommand());
        $application->add(new ImportCommand());
        $application->add(new TranslateCommand());
        $application->add(new FixStructureCommand());
        $application->add(new ListTranslationsCommand());
        $application->add(new MigrateCommand());

        $application->setDefaultCommand('list');

        $application->run();
    }

}
