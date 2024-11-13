<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\CaseStyle;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\CaseStyle\CaseStyleValidatorFactory;
use PHPUnuhi\Components\Validator\CaseStyle\Exception\CaseStyleNotFoundException;
use PHPUnuhi\Components\Validator\CaseStyle\Style\CamelCaseValidator;

class CaseStyleValidatorFactoryTest extends TestCase
{
    /**
     * @throws CaseStyleNotFoundException
     */
    public function testUnknownIdentifierThrowsException(): void
    {
        $factory = new CaseStyleValidatorFactory();

        $this->expectException(CaseStyleNotFoundException::class);

        $factory->fromIdentifier('unknown');
    }

    /**
     * @throws CaseStyleNotFoundException
     */
    public function testValidatorFound(): void
    {
        $factory = new CaseStyleValidatorFactory();

        $validator = $factory->fromIdentifier('camel');

        $this->assertInstanceOf(CamelCaseValidator::class, $validator);
    }
}
