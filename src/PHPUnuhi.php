<?php

declare(strict_types=1);

namespace PHPUnuhi;

class PHPUnuhi
{
    public static function getVersion(): string
    {
        $composerFile = __DIR__ . '/../composer.json';

        $composer = (string)file_get_contents($composerFile);

        $json = json_decode($composer, true);

        return (string)$json['version'];
    }
}
