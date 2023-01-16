<?php

namespace PHPUnuhi\Traits;

use PHPUnuhi\PHPUnuhi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait CommandTrait
{

    /**
     * @return void
     */
    protected function showHeader()
    {
        echo "PHPUnuhi Framework, v" . PHPUnuhi::VERSION . PHP_EOL;
        echo "Copyright (c) 2023 Christian Dangl" . PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getConfigFile(InputInterface $input): string
    {
        $configFile = (string)$input->getOption('configuration') . '';

        if (empty($configFile)) {
            $configFile = 'phpunuhi.xml';
        }

        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        return $workingDir . '/' . $configFile;
    }

    /**
     * @param Command $command
     * @return void
     */
    protected function addTranslatorServiceOptions(Command $command)
    {
        $command->addOption('service', null, InputOption::VALUE_REQUIRED, '', '');

        $command->addOption('google-key', null, InputOption::VALUE_REQUIRED, '', '');
        $command->addOption('deepl-key', null, InputOption::VALUE_REQUIRED, '', '');
        $command->addOption('openai-key', null, InputOption::VALUE_REQUIRED, '', '');
    }

}
