<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\EmptyContent;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;

class AllowEmptyContentTest extends TestCase
{
    public function testKey(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['en-GB', 'de-DE']);

        $this->assertEquals('lblTitle', $data->getKey());
    }


    public function testLocaleFound(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['en-GB', 'de-DE']);

        $this->assertTrue($data->isLocaleAllowed('de-DE'));
    }


    public function testLocaleNotFound(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['en-GB', 'de-DE']);

        $this->assertFalse($data->isLocaleAllowed('fr-FR'));
    }


    public function testLocaleWithWildcardIsAlwaysTrue(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['*']);

        $this->assertTrue($data->isLocaleAllowed('fr-FR'));
    }


    public function testEmptyLocalesIsAlwaysTrue(): void
    {
        $data = new AllowEmptyContent('lblTitle', []);

        $this->assertTrue($data->isLocaleAllowed('fr-FR'));
    }
}
