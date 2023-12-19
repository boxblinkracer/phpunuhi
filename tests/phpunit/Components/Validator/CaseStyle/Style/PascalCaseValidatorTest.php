<?php

namespace phpunit\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\PascalCaseValidator;

class PascalCaseValidatorTest extends TestCase
{

    /**
     * @var CaseStyleValidatorInterface
     */
    private $validator;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new PascalCaseValidator();
    }


    /**
     * @return void
     */
    public function testIdentifier(): void
    {
        $this->assertEquals('pascal', $this->validator->getIdentifier());
    }

    /**
     * @return array[]
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
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $text): void
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }

}
