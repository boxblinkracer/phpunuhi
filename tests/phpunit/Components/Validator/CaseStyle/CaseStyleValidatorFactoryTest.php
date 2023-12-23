<?php

namespace phpunit\Components\Validator\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;

class CaseStyleValidatorFactoryTest extends TestCase
{


    /**
     * @throws CaseStyleNotFoundException
     * @return void
     */
    public function testUnknownIdentifierThrowsException(): void
    {
        $factory = new CaseStyleValidatorFactory();

        $this->expectException(CaseStyleNotFoundException::class);

        $factory->fromIdentifier('unknown');
    }

    /**
     * @throws CaseStyleNotFoundException
     * @return void
     */
    public function testValidatorFound(): void
    {
        $factory = new CaseStyleValidatorFactory();

        $validator = $factory->fromIdentifier('camel');

        $this->assertInstanceOf(CamelCaseValidator::class, $validator);
    }
}
