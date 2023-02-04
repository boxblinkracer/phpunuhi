<?php

namespace phpunit\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\KebabCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\LowerCaseValidator;
use PHPUnuhi\Components\Validator\CaseStyle\Style\SnakeCaseValidator;

class LowerCaseValidatorTest extends TestCase
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
        $this->validator = new LowerCaseValidator();
    }


    /**
     * @return void
     */
    public function testIdentifier()
    {
        $this->assertEquals('lower', $this->validator->getIdentifier());
    }

    /**
     * @return array[]
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
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $text)
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }

}
