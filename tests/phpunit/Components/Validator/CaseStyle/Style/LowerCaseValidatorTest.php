<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\Style\LowerCaseValidator;

class LowerCaseValidatorTest extends TestCase
{
    private LowerCaseValidator $validator;



    protected function setUp(): void
    {
        $this->validator = new LowerCaseValidator();
    }



    public function testIdentifier(): void
    {
        $this->assertEquals('lower', $this->validator->getIdentifier());
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return [
            [true, 'text'],
            [true, 'text_style'],
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
