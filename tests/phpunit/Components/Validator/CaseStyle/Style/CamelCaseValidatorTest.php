<?php

namespace phpunit\Components\Validator\CaseStyle\Style;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorInterface;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;

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
     * @param bool $expectedValid
     * @param string $text
     * @return void
     */
    public function testIsValid(bool $expectedValid, string $text): void
    {
        $isValid = $this->validator->isValid($text);

        $this->assertEquals($expectedValid, $isValid);
    }
}
