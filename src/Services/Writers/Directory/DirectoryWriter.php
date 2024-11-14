<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\Directory;

class DirectoryWriter implements DirectoryWriterInterface
{
    public function createDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
