<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\Directory;

interface DirectoryWriterInterface
{
    public function createDirectory(string $path): void;
}
