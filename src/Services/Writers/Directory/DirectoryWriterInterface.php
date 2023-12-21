<?php

namespace PHPUnuhi\Services\Writers\Directory;

interface DirectoryWriterInterface
{

    /**
     * @param string $path
     * @return void
     */
    public function createDirectory(string $path): void;

}