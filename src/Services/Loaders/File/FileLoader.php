<?php

namespace PHPUnuhi\Services\Loaders\File;

use Exception;

class FileLoader
{

    /**
     * @param string $filename
     * @throws Exception
     * @return string
     */
    public function load(string $filename): string
    {
        if (!file_exists($filename)) {
            throw new Exception('Configuration file not found: ' . $filename);
        }

        return (string)file_get_contents($filename);
    }
}
