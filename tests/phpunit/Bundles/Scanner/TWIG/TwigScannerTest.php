<?php

namespace phpunit\Bundles\Scanner\TWIG;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Twig\TwigScanner;

class TwigScannerTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetScannerName(): void
    {
        $this->assertEquals('twig', (new TwigScanner())->getScannerName());
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
     * @param string $text
     * @return void
     */
    public function testTranslationFound(string $text): void
    {
        $finder = new TwigScanner();

        $found = $finder->findKey('header.example', $text);

        $this->assertTrue($found);
    }

    /**
     * @return void
     */
    public function testTranslationNotFound(): void
    {
        $text = "This is a sample {{ ' header.example ' | trans }} text.";

        $finder = new TwigScanner();

        $found = $finder->findKey('header.example2', $text);

        $this->assertFalse($found);
    }
}
