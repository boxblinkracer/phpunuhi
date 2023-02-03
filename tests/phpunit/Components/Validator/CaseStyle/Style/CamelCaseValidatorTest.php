<?php

namespace phpunit\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;

class CamelCaseValidatorTest extends TestCase
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
        $this->validator = new CamelCaseValidator();
    }


    /**
     * @return void
     */
    public function testIdentifier()
    {
        $this->assertEquals('camel', $this->validator->getIdentifier());
    }

    /**
     * @return array[]
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
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $text)
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }

}
