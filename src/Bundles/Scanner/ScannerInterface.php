<?php

namespace PHPUnuhi\Bundles\Twig;

interface ScannerInterface
{

    /**
     * Returns a unique name for the scanner.
     * @return string
     */
    public function getScannerName(): string;

    /**
     * @return string
     */
    public function getExtension(): string;

    /**
     * @param string $key
     * @param string $content
     * @return bool
     */
    public function findKey(string $key, string $content): bool;
}
