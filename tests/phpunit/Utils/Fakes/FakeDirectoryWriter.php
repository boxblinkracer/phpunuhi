<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Services\Writers\Directory\DirectoryWriterInterface;

class FakeDirectoryWriter implements DirectoryWriterInterface
{
    private string $createdDirectory = '';



    public function getCreatedDirectory(): string
    {
        return $this->createdDirectory;
    }


    public function createDirectory(string $path): void
    {
        $this->createdDirectory = $path;
    }
}
