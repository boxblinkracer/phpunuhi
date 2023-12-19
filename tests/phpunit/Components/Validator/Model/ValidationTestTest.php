<?php

namespace phpunit\Components\Validator\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\Model\ValidationTest;

class ValidationTestTest extends TestCase
{


    /**
     * @var ValidationTest
     */
    private $test;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->test = new ValidationTest(
            'btnCancel',
            'en_US',
            'Testing btnCancel',
            'en.json',
            15,
            'EXISTING',
            'It was not existing',
            true
        );
    }

    /**
     * @return void
     */
    public function testFilename(): void
    {
        $this->assertEquals('en.json', $this->test->getFilename());
    }

    /**
     * @return void
     */
    public function testLineNumber(): void
    {
        $this->assertEquals(15, $this->test->getLineNumber());
    }

    /**
     * @return void
     */
    public function testClassification(): void
    {
        $this->assertEquals('EXISTING', $this->test->getClassification());
    }

    /**
     * @return void
     */
    public function testTitle(): void
    {
        $this->assertEquals('[en_US] Testing btnCancel', $this->test->getTitle());
    }

    /**
     * @return void
     */
    public function testFailureMessage(): void
    {
        $this->assertEquals('It was not existing', $this->test->getFailureMessage());
    }

    /**
     * @return void
     */
    public function testTranslationKey(): void
    {
        $this->assertEquals('btnCancel', $this->test->getTranslationKey());
    }

    /**
     * @return void
     */
    public function testSuccess(): void
    {
        $this->assertTrue($this->test->isSuccess());
    }
}