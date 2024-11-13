<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\File;

class FileWriter implements FileWriterInterface
{
    public function writeFile(string $filename, string $content): void
    {
        file_put_contents($filename, $content);
    }
}
