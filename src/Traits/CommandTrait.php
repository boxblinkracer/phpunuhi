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
        echo "Copyright (c) 2023 - " . date('Y') . ", Christian Dangl and contributors" . PHP_EOL;
        echo PHP_EOL;
    }


    protected function getConfigFile(InputInterface $input): string
    {
        $configFile = $input->getOption('configuration');

        if (!is_string($configFile)) {
            $configFile = '';
        }

        $configFile = trim($configFile);

        if ($configFile === '' || $configFile === '0') {
            $configFile = 'phpunuhi.xml';
        }

        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        return $workingDir . '/' . $configFile;
    }


    protected function getFromRelativePath(string $path): string
    {
        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        $dir = $workingDir . '/' . $path;

        return (string)realpath($dir);
    }


    protected function getConfigStringValue(string $name, InputInterface $input): string
    {
        $value = $input->getOption($name);

        if (!is_string($value)) {
            return '';
        }

        return $value;
    }


    protected function getConfigIntValue(string $name, InputInterface $input, int $default): int
    {
        if (!$input->hasOption($name)) {
            return $default;
        }

        $value = $input->getOption($name);

        if (!ctype_digit($value)) {
            return $default;
        }

        return (int)$value;
    }


    protected function getConfigBoolValue(string $name, InputInterface $input): bool
    {
        $value = $input->getOption($name);

        if (!is_bool($value)) {
            return false;
        }

        return $value;
    }


    protected function getConfigFloatValue(string $name, InputInterface $input, float $default): float
    {
        if (!$input->hasOption($name)) {
            return $default;
        }

        $value = $input->getOption($name);

        if (!ctype_digit($value)) {
            return $default;
        }

        return (float)$value;
    }
}
