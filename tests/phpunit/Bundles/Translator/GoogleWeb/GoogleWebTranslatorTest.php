<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Translator\GoogleWeb;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Translator\GoogleWeb\GoogleWebTranslator;

class GoogleWebTranslatorTest extends TestCase
{
    public function testGetName(): void
    {
        $translator = new GoogleWebTranslator();

        $this->assertEquals('googleweb', $translator->getName());
    }


    public function testGetOptions(): void
    {
        $translator = new GoogleWebTranslator();

        $foundOptions = $translator->getOptions();

        $this->assertCount(0, $foundOptions);
    }
}
