<?php

namespace phpunit\Components\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\RulesValidator;

class RulesValidatorTest extends TestCase
{

    /**
     * @return void
     */
    public function testTypeIdentifier(): void
    {
        $validator = new RulesValidator();

        $this->assertEquals('RULE', $validator->getTypeIdentifier());
    }

}
