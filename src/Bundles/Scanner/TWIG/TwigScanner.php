<?php

namespace PHPUnuhi\Bundles\Twig;

class TwigScanner implements ScannerInterface
{

    /**
     * @return string
     */
    public function getScannerName(): string
    {
        return 'twig';
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'twig';
    }

    /**
     * {{ 'header.example' | trans }}
     *
     * @param string $key
     * @param string $content
     * @return bool
     */
    public function findKey(string $key, string $content): bool
    {
        $content = str_replace(" ", '', $content);
        $content = str_replace('"', "'", $content);

        $pattern = '/{{\s*\'?' . preg_quote($key, '/') . '\'?\s*\|\s*.*trans.*\s*}}/';

        $matches = [];

        preg_match($pattern, $content, $matches);

        return (!empty($matches));
    }
}
