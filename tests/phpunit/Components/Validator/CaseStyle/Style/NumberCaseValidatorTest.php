<?php

namespace phpunit\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\NumberCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\SnakeCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\StartCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\UpperCaseValidator;

class NumberCaseValidatorTest extends TestCase
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
        $this->validator = new NumberCaseValidator();
    }


    /**
     * @return void
     */
    public function testIdentifier()
    {
        $this->assertEquals('number', $this->validator->getIdentifier());
    }

    /**
     * @return array[]
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
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $text)
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }

}
