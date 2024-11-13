<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\CaseStyle;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\CaseStyle\LowerCaseConverter;

class LowerCaseConverterTest extends TestCase
{
    public function testIdentifier(): void
    {
        $converter = new LowerCaseConverter();

        $this->assertEquals('lower', $converter->getIdentifier());
    }

    /**
     * @return array<mixed>>
     */
    public function getData(): array
    {
        return [
            ['btn-title', 'btn-title'],
            ['btn-title', 'BTN-TITLE'],
            ['btn_title', 'btn_title'],
            ['btn_title', 'BTN_TITLE'],
            ['btntitle', 'btnTitle'],
            ['btn title', 'BTN TITLE'],
        ];
    }

    /**
     * @dataProvider getData
     *
     * @throws Exception
     */
    public function testConvert(string $expected, string $text): void
    {
        $converter = new LowerCaseConverter();

        $newText = $converter->convert($text);

        $this->assertEquals($expected, $newText);
    }
}
