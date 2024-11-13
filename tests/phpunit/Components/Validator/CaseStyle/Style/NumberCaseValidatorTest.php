<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\NumberCaseValidator;

class NumberCaseValidatorTest extends TestCase
{
    private NumberCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new NumberCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('number', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, '0'],
            [true, '15'],
            [true, '-1'],
            [false, 'text0'],
            [false, '0text'],
            [false, 'text'],
            [false, '0_text'],
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
