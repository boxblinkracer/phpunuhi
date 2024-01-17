<?php

namespace PHPUnuhi\Bundles\Twig;

interface ScannerInterface
{

    /**
     * Returns a unique name for the storage.
     * @return string
     */
    public function getStorageName(): string;

    /**
     * @param string $key
     * @param string $content
     * @return bool
     */
    public function findKey(string $key, string $content): bool;
}
