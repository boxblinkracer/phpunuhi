<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;

class CamelCaseValidatorTest extends TestCase
{
    private CamelCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new CamelCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('camel', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'textStyle'],
            [true, 'text'],
            [false, 'text-style'],
            [false, 'text_style'],
            [false, 'TEXT-STYLE'],
            [false, 'TextStyle'],
        ];
    }

    /**
     * @dataProvider getData
     *
     */
    public function testIsValid(bool $expectedValid, string $text): void
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }
}
