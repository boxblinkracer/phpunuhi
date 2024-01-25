<?php

namespace phpunit\Components\Validator\EmptyContent;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;

class AllowEmptyContentTest extends TestCase
{

    /**
     * @return void
     */
    public function testKey(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['en-GB', 'de-DE']);

        $this->assertEquals('lblTitle', $data->getKey());
    }

    /**
     * @return void
     */
    public function testLocaleFound(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['en-GB', 'de-DE']);

        $this->assertTrue($data->isLocaleAllowed('de-DE'));
    }

    /**
     * @return void
     */
    public function testLocaleNotFound(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['en-GB', 'de-DE']);

        $this->assertFalse($data->isLocaleAllowed('fr-FR'));
    }

    /**
     * @return void
     */
    public function testLocaleWithWildcardIsAlwaysTrue(): void
    {
        $data = new AllowEmptyContent('lblTitle', ['*']);

        $this->assertTrue($data->isLocaleAllowed('fr-FR'));
    }

    /**
     * @return void
     */
    public function testEmptyLocalesIsAlwaysTrue(): void
    {
        $data = new AllowEmptyContent('lblTitle', []);

        $this->assertTrue($data->isLocaleAllowed('fr-FR'));
    }
}
