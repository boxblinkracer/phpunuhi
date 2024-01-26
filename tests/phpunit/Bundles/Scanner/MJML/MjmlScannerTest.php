<?php

namespace phpunit\Bundles\Scanner\MJML;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\MJML\MjmlScanner;

class MjmlScannerTest extends TestCase
{

    /**
     * @return void
     */
    public function testGetScannerName(): void
    {
        $this->assertEquals('mjml', (new MjmlScanner())->getScannerName());
    }

    /**
     * @return void
     */
    public function testGetExtension(): void
    {
        $this->assertEquals('mjml', (new MjmlScanner())->getExtension());
    }

    /**
     * @return array<mixed>
     */
    public function getFoundData(): array
    {
        return [
            ["<h1>{{'email.contact.subject'|trans|sw_sanitize}}</h1>"],
            ["<h1>{{ 'email.contact.subject' | trans | sw_sanitize }}</h1>"],
            ["<h1>{{ 'email.contact.subject'|trans|sw_sanitize }}</h1>"],

            ["<h1>{{ 'email.contact.subject ' | trans }}</h1>"],
            ["<h1>{{ ' email.contact.subject' | trans }}</h1>"],
            ["<h1>{{ ' email.contact.subject ' | trans }}</h1>"],

            ["<h1>{{ 'email.contact.subject' | trans | }}</h1>"],
            ["<h1>{{ 'email.contact.subject' | sw_sanitize | trans | }}</h1>"],
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
        $finder = new MjmlScanner();

        $found = $finder->findKey('email.contact.subject', $text);

        $this->assertTrue($found);
    }

    /**
     * @return void
     */
    public function testTranslationNotFound(): void
    {
        $text = "This is a sample {{ 'email.contact.subject' | trans | sw_sanitize }} text.";

        $finder = new MjmlScanner();

        $found = $finder->findKey('header.example2', $text);

        $this->assertFalse($found);
    }
}
