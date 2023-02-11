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

        if (file_exists($composerFile)) {
            $composer = file_get_contents($composerFile);

            $json = json_decode($composer, true);

            return (string)$json['version'];
        }

        return 'unknown';
    }

}
