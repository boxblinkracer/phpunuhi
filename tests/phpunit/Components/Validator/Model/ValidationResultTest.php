<?php

namespace phpunit\Components\Validator\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\Model\ValidationError;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;

class ValidationResultTest extends TestCase
{


    /**
     * @var ValidationResult
     */
    private $result;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $test = new ValidationTest(
            '',
            '',
            '',
            '',
            5,
            '',
            '',
            true
        );

        $error = new ValidationError(
            '',
            '',
            '',
            '',
            '',
            4
        );

        $this->result = new ValidationResult([$test], [$error]);
    }

    /**
     * @return void
     */
    public function testGetTests(): void
    {
        $this->assertCount(1, $this->result->getTests());
    }

    /**
     * @return void
     */
    public function testGetErrors(): void
    {
        $this->assertCount(1, $this->result->getErrors());
    }
}
