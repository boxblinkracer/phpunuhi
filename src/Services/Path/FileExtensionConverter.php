<?php

namespace PHPUnuhi\Services\Path;

class FileExtensionConverter
{

    /**
     * @param string $filename
     * @param string $extension
     * @return string
     */
    public function convert(string $filename, string $extension): string
    {
        $directory = pathinfo($filename, PATHINFO_DIRNAME);
        $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

        $newFilename = $directory . '/' . $filenameWithoutExtension . '.' . $extension;

        return $newFilename;
    }

}