<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\SnakeCaseValidator;

class SnakeCaseValidatorTest extends TestCase
{
    private SnakeCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new SnakeCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('snake', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'text_style'],
            [true, 'text'],
            [false, 'text-style'],
            [false, 'textStyle'],
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
