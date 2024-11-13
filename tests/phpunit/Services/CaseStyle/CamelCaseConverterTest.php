<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\CaseStyle\CamelCaseConverter;

class CamelCaseConverterTest extends TestCase
{
    public function testIdentifier(): void
    {
        $converter = new CamelCaseConverter();

        $this->assertEquals('camel', $converter->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            ['btnTitle', 'btn-title'],
            ['btnTitle', 'BTN-TITLE'],
            ['btnTitle', 'btn_title'],
            ['btnTitle', 'BTN_TITLE'],
            ['btnTitle', 'btnTitle'],
            ['btnTitle', 'btn title'],
        ];
    }

    /**
     * @dataProvider getData
     *
     */
    public function testConvertCamelCase(string $expected, string $text): void
    {
        $converter = new CamelCaseConverter();

        $converted = $converter->convert($text);

        $this->assertEquals($expected, $converted);
    }
}
