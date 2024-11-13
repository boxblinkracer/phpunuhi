<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Scanner\TWIG;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Twig\TwigScanner;

class TwigScannerTest extends TestCase
{
    public function testGetScannerName(): void
    {
        $this->assertEquals('twig', (new TwigScanner())->getScannerName());
    }


    public function testGetExtension(): void
    {
        $this->assertEquals('twig', (new TwigScanner())->getExtension());
    }

    /**
     * @return array<mixed>
     */
    public function getFoundData(): array
    {
        return [
            ["This is a sample {{ 'header.example' | trans }} text."],
            ["This is a sample {{ ' header.example ' | trans }} text."],
            ["This is a sample {{' header.example' | trans }} text."],
            ["This is a sample {{'header.example ' | trans }} text."],

            ['This is a sample {{ "header.example " | trans }} text.'],
            ['This is a sample {{" header.example" | trans }} text.'],
            ['This is a sample {{"header.example " | trans }} text.'],
            ['This is a sample {{"header.example" | trans }} text.'],

            ['This is a sample {{"header.example" | trans | raw }} text.'],
            ['This is a sample {{"header.example" | raw | trans }} text.'],
        ];
    }

    /**
     * @dataProvider getFoundData
     *
     */
    public function testTranslationFound(string $text): void
    {
        $finder = new TwigScanner();

        $found = $finder->findKey('header.example', $text);

        $this->assertTrue($found);
    }


    public function testTranslationNotFound(): void
    {
        $text = "This is a sample {{ ' header.example ' | trans }} text.";

        $finder = new TwigScanner();

        $found = $finder->findKey('header.example2', $text);

        $this->assertFalse($found);
    }
}
