<?php

namespace phpunit\Services\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\CaseStyle\CamelCaseConverter;

class CamelCaseConverterTest extends TestCase
{


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
     * @param string $expected
     * @param string $text
     * @return void
     */
    public function testConvertCamelCase(string $expected, string $text): void
    {
        $converter = new CamelCaseConverter();

        $converted = $converter->convert($text);

        $this->assertEquals($expected, $converted);
    }
}
