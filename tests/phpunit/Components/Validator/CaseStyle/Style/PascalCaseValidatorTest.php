<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\PascalCaseValidator;

class PascalCaseValidatorTest extends TestCase
{
    private PascalCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new PascalCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('pascal', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'Text'],
            [true, 'TextStyle'],
            [false, 'text'],
            [false, 'text-style'],
            [false, 'textStyle'],
            [false, 'text_style'],
            [false, 'TEXT-STYLE'],
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testIsValid(bool $expectedValid, string $text): void
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }
}
