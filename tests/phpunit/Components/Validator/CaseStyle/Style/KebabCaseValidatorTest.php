<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;

class KebabCaseValidatorTest extends TestCase
{
    private KebabCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new KebabCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('kebab', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'text-style'],
            [true, 'text'],
            [false, 'textStyle'],
            [false, 'text_style'],
            [false, 'TEXT-STYLE'],
            [false, 'TextStyle'],
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
