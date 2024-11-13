<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\CaseStyle;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\CaseStyle\UpperCaseConverter;

class UpperCaseConverterTest extends TestCase
{
    public function testIdentifier(): void
    {
        $converter = new UpperCaseConverter();

        $this->assertEquals('upper', $converter->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            ['BTN-TITLE', 'btn-title'],
            ['BTN-TITLE', 'BTN-TITLE'],
            ['BTN_TITLE', 'btn_title'],
            ['BTN_TITLE', 'BTN_TITLE'],
            ['BTNTITLE', 'btnTitle'],
            ['BTN TITLE', 'btn title'],
        ];
    }

    /**
     * @dataProvider getData
     *
     * @throws Exception
     */
    public function testConvert(string $expected, string $text): void
    {
        $converter = new UpperCaseConverter();

        $newText = $converter->convert($text);

        $this->assertEquals($expected, $newText);
    }
}
