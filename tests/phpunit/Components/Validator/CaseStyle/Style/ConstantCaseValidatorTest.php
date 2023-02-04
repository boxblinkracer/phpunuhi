<?php

namespace phpunit\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\ConstantCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;

class ConstantCaseValidatorTest extends TestCase
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
        $this->validator = new ConstantCaseValidator();
    }


    /**
     * @return void
     */
    public function testIdentifier()
    {
        $this->assertEquals('constant', $this->validator->getIdentifier());
    }

    /**
     * @return array[]
     */
    public function getData(): array
    {
        return [
            [true, 'TEXTSTYLE'],
            [true, 'TEXT_STYLE'],
            [false, 'TEXT-STYLE'],
            [false, 'text-style'],
            [false, 'text'],
            [false, 'textStyle'],
            [false, 'text_style'],
            [false, 'TextStyle'],
        ];
    }

    /**
     * @dataProvider getData
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $text)
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }

}
