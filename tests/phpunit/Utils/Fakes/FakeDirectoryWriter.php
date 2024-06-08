<?php

namespace PHPUnuhi\Tests\Utils\Fakes;

use PHPUnuhi\Services\Writers\Directory\DirectoryWriterInterface;

class FakeDirectoryWriter implements DirectoryWriterInterface
{

    /**
     * @var string
     */
    private $createdDirectory;


    /**
     * @return string
     */
    public function getCreatedDirectory(): string
    {
        return $this->createdDirectory;
    }

    /**
     * @param string $path
     * @return void
     */
    public function createDirectory(string $path): void
    {
        $this->createdDirectory = $path;
    }
}
