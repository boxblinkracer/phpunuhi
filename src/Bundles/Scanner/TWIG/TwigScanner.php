<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Twig;

class TwigScanner implements ScannerInterface
{
    public function getScannerName(): string
    {
        return 'twig';
    }


    public function getExtension(): string
    {
        return 'twig';
    }

    /**
     * {{ 'header.example' | trans }}
     *
     */
    public function findKey(string $key, string $content): bool
    {
        $content = str_replace(" ", '', $content);
        $content = str_replace('"', "'", $content);

        $pattern = '/{{\s*\'?' . preg_quote($key, '/') . '\'?\s*\|\s*.*trans.*\s*}}/';

        $matches = [];

        preg_match($pattern, $content, $matches);

        return ($matches !== []);
    }
}
