<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\Model\ValidationResult;
use PHPUnuhi\Components\Validator\Model\ValidationTest;

class ValidationResultTest extends TestCase
{
    private ValidationResult $result;



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
            false
        );

        $this->result = new ValidationResult([$test]);
    }


    public function testGetTests(): void
    {
        $this->assertCount(1, $this->result->getTests());
    }


    public function testGetErrors(): void
    {
        $this->assertCount(1, $this->result->getErrors());
    }
}
