<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

use PHPUnuhi\PHPUnuhi;
use Symfony\Component\Console\Input\InputInterface;

trait CommandTrait
{

    /**
     * @return void
     */
    protected function showHeader()
    {
        echo "PHPUnuhi Framework, v" . PHPUnuhi::getVersion() . PHP_EOL;
        echo "Copyright (c) 2023 Christian Dangl" . PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getConfigFile(InputInterface $input): string
    {
        $configFile = $input->getOption('configuration');

        if (!is_string($configFile)) {
            $configFile = '';
        }

        if (empty($configFile)) {
            $configFile = 'phpunuhi.xml';
        }

        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        return $workingDir . '/' . $configFile;
    }

    /**
     * @param string $name
     * @param InputInterface $input
     * @return string
     */
    protected function getConfigStringValue(string $name, InputInterface $input): string
    {
        $value = $input->getOption($name);

        if (!is_string($value)) {
            return '';
        }

        return $value;
    }

}
