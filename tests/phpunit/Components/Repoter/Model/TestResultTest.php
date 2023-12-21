<?php

namespace phpunit\Components\Repoter\Model;

use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\TestRunner;
use PHPUnuhi\Components\Reporter\Model\SuiteResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;

class TestResultTest extends TestCase
{

    /**
     * @var TestResult
     */
    private $resultFailed;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->resultFailed = new TestResult(
            'storefront 1',
            'btnCancel',
            'ErrorClass',
            14,
            'EXCEPTION',
            'this is an error',
            false
        );
    }

    /**
     * @return void
     */
    public function testName(): void
    {
        $this->assertEquals('storefront 1', $this->resultFailed->getName());
    }

    /**
     * @return void
     */
    public function testClassName(): void
    {
        $this->assertEquals('ErrorClass', $this->resultFailed->getClassName());
    }

    /**
     * @return void
     */
    public function testFailureMessage(): void
    {
        $this->assertEquals('this is an error', $this->resultFailed->getFailureMessage());
    }

    /**
     * @return void
     */
    public function testFailureType(): void
    {
        $this->assertEquals('EXCEPTION', $this->resultFailed->getFailureType());
    }

    /**
     * @return void
     */
    public function testLineNumber(): void
    {
        $this->assertEquals(14, $this->resultFailed->getLineNumber());
    }

    /**
     * @return void
     */
    public function testTranslationKey(): void
    {
        $this->assertEquals('btnCancel', $this->resultFailed->getTranslationKey());
    }

}