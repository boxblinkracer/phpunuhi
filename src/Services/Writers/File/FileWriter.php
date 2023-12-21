<?php

namespace PHPUnuhi\Services\Writers\File;

class FileWriter implements FileWriterInterface
{

    /**
     * @param string $filename
     * @param string $content
     * @return void
     */
    public function writeFile(string $filename, string $content): void
    {
        file_put_contents($filename, $content);
    }

}
