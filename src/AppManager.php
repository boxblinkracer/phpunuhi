<?php

namespace PHPUnuhi;

use Exception;
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

        $application->add(new ValidateCommand());

        $application->setDefaultCommand('validate');

        $application->run();
    }

}
