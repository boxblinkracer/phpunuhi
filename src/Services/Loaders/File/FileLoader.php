<?php

namespace PHPUnuhi\Services\Loaders\File;

class FileLoader
{

    /**
     * @param string $filename
     * @return string
     */
    public function load(string $filename): string
    {
        $content = file_get_contents($filename);

        if ($content === false) {
            return '';
        }

        return $content;
    }
}
