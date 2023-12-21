<?php

namespace PHPUnuhi\Services\Writers\Directory;

class DirectoryWriter implements DirectoryWriterInterface
{

    /**
     * @param string $path
     * @return void
     */
    public function createDirectory(string $path): void
    {
        mkdir($path);
    }

}