<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\Loaders\Directory;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoryLoader
{
    /**
     * @return array<mixed>
     */
    public function getFiles(string $directory, string $extension): array
    {
        $files = [];

        if (is_dir($directory)) {
            // Open the folder
            $directoryIterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

            // Iterate through files
            foreach ($iterator as $file) {
                // Check if it's a file and has the specified extension
                if (!$file->isFile()) {
                    continue;
                }
                if (pathinfo($file, PATHINFO_EXTENSION) != $extension) {
                    continue;
                }
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
