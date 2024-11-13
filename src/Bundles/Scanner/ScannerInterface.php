<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Twig;

interface ScannerInterface
{
    /**
     * Returns a unique name for the scanner.
     */
    public function getScannerName(): string;


    public function getExtension(): string;


    public function findKey(string $key, string $content): bool;
}
