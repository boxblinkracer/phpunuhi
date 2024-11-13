<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Repoter\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\Model\ReportTestResult;

class ReportTestResultTest extends TestCase
{
    private ReportTestResult $resultFailed;


    public function setUp(): void
    {
        $this->resultFailed = new ReportTestResult(
            'storefront 1',
            'btnCancel',
            'ErrorClass',
            14,
            'EXCEPTION',
            'this is an error',
            false
        );
    }


    public function testName(): void
    {
        $this->assertEquals('storefront 1', $this->resultFailed->getName());
    }


    public function testClassName(): void
    {
        $this->assertEquals('ErrorClass', $this->resultFailed->getClassName());
    }


    public function testFailureMessage(): void
    {
        $this->assertEquals('this is an error', $this->resultFailed->getFailureMessage());
    }


    public function testFailureType(): void
    {
        $this->assertEquals('EXCEPTION', $this->resultFailed->getFailureType());
    }


    public function testLineNumber(): void
    {
        $this->assertEquals(14, $this->resultFailed->getLineNumber());
    }


    public function testTranslationKey(): void
    {
        $this->assertEquals('btnCancel', $this->resultFailed->getTranslationKey());
    }
}
