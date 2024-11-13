<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\NoneCaseValidator;

class NoneCaseValidatorTest extends TestCase
{
    private NoneCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new NoneCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('none', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'TEXTSTYLE'],
            [true, 'TEXT-STYLE'],
            [true, 'TEXT-STYLE'],
            [true, 'text-style'],
            [true, 'text'],
            [true, 'textStyle'],
            [true, 'text_style'],
            [true, 'TextStyle'],
            [true, '1Column'],
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
