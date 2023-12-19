<?php

namespace phpunit\Components\Validator\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;

class CaseStyleValidatorFactoryTest extends TestCase
{


    /**
     * @return void
     * @throws CaseStyleNotFoundException
     */
    public function testUnknownIdentifierThrowsException()
    {
        $factory = new CaseStyleValidatorFactory('', [], '');

        $this->expectException(CaseStyleNotFoundException::class);

        $factory->fromIdentifier('unknown');
    }

    /**
     * @return void
     * @throws CaseStyleNotFoundException
     */
    public function testValidatorFound()
    {
        $factory = new CaseStyleValidatorFactory('', [], '');

        $validator = $factory->fromIdentifier('camel');

        $this->assertInstanceOf(CamelCaseValidator::class, $validator);
    }
}