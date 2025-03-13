<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage;

trait FileBasedStorageAvailableTrait
{
    public function storageAvailable(string $dsn): bool
    {
        $parts = parse_url($dsn);

        if ('file' !== $parts['scheme']) {
            return false;
        }

        $filename = (string)$parts['path'];

        return is_file($filename) && is_readable($filename);
    }
}
