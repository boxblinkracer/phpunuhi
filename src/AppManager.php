<?php

namespace PHPUnuhi;

use Exception;
use PHPUnuhi\Commands\ValidateCommand;
use SVRUnit\Commands\ExportCommand;
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

        $application->add(new ValidateCommand());
        $application->add(new ExportCommand());

        $application->setDefaultCommand('validate');

        $application->run();
    }

}
