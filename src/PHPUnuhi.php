<?php

namespace PHPUnuhi;

class PHPUnuhi
{

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        $composerFile = __DIR__ . '/../composer.json';

        $composer = (string)file_get_contents($composerFile);

        $json = json_decode($composer, true);

        return (string)$json['version'];
    }
}
