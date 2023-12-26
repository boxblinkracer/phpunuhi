<?php

namespace phpunit\Services\CaseStyle;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\CaseStyle\LowerCaseConverter;

class LowerCaseConverterTest extends TestCase
{


    /**
     * @return void
     */
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
     * @param string $expected
     * @param string $text
     * @throws Exception
     * @return void
     */
    public function testConvert(string $expected, string $text): void
    {
        $converter = new LowerCaseConverter();

        $newText = $converter->convert($text);

        $this->assertEquals($expected, $newText);
    }
}
