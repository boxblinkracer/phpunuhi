<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\MJML;

use PHPUnuhi\Bundles\Twig\ScannerInterface;

class MjmlScanner implements ScannerInterface
{
    public function getScannerName(): string
    {
        return 'mjml';
    }


    public function getExtension(): string
    {
        return 'mjml';
    }

    /**
     * {{ 'email.contact.subject' | trans | sw_sanitize }}
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
