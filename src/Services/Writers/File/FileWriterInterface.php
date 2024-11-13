<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Writers\File;

interface FileWriterInterface
{
    public function writeFile(string $filename, string $content): void;
}
