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
     * {{ translate({ ident: 'header.example' }) }}
     * help_id('header.example')
     * help_text('header.example')
     *
     */
    public function findKey(string $key, string $content): bool
    {
        $content = str_replace(" ", '', $content);
        $content = str_replace('"', "'", $content);

        $defaultPattern = '{{\s*\'?' . preg_quote($key, '/') . '\'?\s*\|\s*.*trans.*\s*}}';
        $oxidPattern1 = '{{\s*translate\({\s*ident:\s*([\'"])?'.preg_quote($key, '/').'([\'"])?\s*}\)\s*}}';
        $oxidPattern2 = 'help_(id|text)\(\s*([\'"])?'.preg_quote($key, '/').'([\'"])?\)';

        $pattern = '/('.implode(
            ')|(',
            [$defaultPattern, $oxidPattern1, $oxidPattern2],
        ).')/';

        $matches = [];
        preg_match($pattern, $content, $matches);

        return ($matches !== []);
    }
}
