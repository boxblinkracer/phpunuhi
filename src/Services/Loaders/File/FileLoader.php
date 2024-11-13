<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Loaders\File;

use Exception;

class FileLoader
{
    /**
     * @throws Exception
     */
    public function load(string $filename): string
    {
        if (!file_exists($filename)) {
            throw new Exception('Configuration file not found: ' . $filename);
        }

        return (string)file_get_contents($filename);
    }
}
