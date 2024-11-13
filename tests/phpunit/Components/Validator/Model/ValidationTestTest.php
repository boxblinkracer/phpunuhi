<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Validator\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Validator\Model\ValidationTest;
use PHPUnuhi\Models\Translation\Locale;

class ValidationTestTest extends TestCase
{
    private ValidationTest $test;



    public function setUp(): void
    {
        $locale = new Locale('en_US', false, '', '');

        $this->test = new ValidationTest(
            'btnCancel',
            $locale,
            'Testing btnCancel',
            'en.json',
            15,
            'EXISTING',
            'It was not existing',
            true
        );
    }


    public function testFilename(): void
    {
        $this->assertEquals('en.json', $this->test->getFilename());
    }


    public function testLineNumber(): void
    {
        $this->assertEquals(15, $this->test->getLineNumber());
    }


    public function testClassification(): void
    {
        $this->assertEquals('EXISTING', $this->test->getClassification());
    }


    public function testTitle(): void
    {
        $this->assertEquals('[en_US] Testing btnCancel', $this->test->getTitle());
    }


    public function testFailureMessage(): void
    {
        $this->assertEquals('It was not existing', $this->test->getFailureMessage());
    }


    public function testTranslationKey(): void
    {
        $this->assertEquals('btnCancel', $this->test->getTranslationKey());
    }


    public function testSuccess(): void
    {
        $this->assertTrue($this->test->isSuccess());
    }
}
