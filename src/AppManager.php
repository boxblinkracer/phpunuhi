<?php

namespace PHPUnuhi;

use Exception;
use PHPUnuhi\Commands\ExportCommand;
use PHPUnuhi\Commands\FixStructureCommand;
use PHPUnuhi\Commands\ImportCommand;
use PHPUnuhi\Commands\StatusCommand;
use PHPUnuhi\Commands\TranslateCommand;
use PHPUnuhi\Commands\ValidateCommand;
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
        $application = new Application('PHPUnuhi', PHPUnuhi::VERSION);

        $application->add(new StatusCommand());
        $application->add(new ValidateCommand());
        $application->add(new ExportCommand());
        $application->add(new ImportCommand());
        $application->add(new TranslateCommand());
        $application->add(new FixStructureCommand());

        $application->setDefaultCommand('list');

        $application->run();
    }

}
