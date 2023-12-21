<?php

namespace PHPUnuhi\Services\Writers\File;

interface FileWriterInterface
{

    /**
     * @param string $filename
     * @param string $content
     * @return void
     */
    public function writeFile(string $filename, string $content): void;
}
