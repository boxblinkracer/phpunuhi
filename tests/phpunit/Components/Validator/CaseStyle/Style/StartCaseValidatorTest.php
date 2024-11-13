<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\StartCaseValidator;

class StartCaseValidatorTest extends TestCase
{
    private StartCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new StartCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('start', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'Text'],
            [true, 'Textstyle'],
            [false, 'TextStyle'],
            [false, 'TEXT-STYLE'],
            [false, 'text-style'],
            [false, 'text'],
            [false, 'textStyle'],
            [false, 'text_style'],
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
