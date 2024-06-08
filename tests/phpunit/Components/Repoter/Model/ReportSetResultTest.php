<?php

namespace PHPUnuhi\Tests\Components\Repoter\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\Model\ReportSetResult;
use PHPUnuhi\Components\Reporter\Model\ReportTestResult;

class ReportSetResultTest extends TestCase
{


    /**
     * @var ReportSetResult
     */
    private $result;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->result = new ReportSetResult('storefront');

        $this->result->addTestResult(
            new ReportTestResult(
                'storefront',
                'btnCancel',
                'ErrorClass',
                14,
                'EXCEPTION',
                'this is an error',
                false
            )
        );

        $this->result->addTestResult(
            new ReportTestResult(
                'storefront',
                'btnOk',
                'ErrorClass',
                23,
                '',
                '',
                true
            )
        );
    }

    /**
     * @return void
     */
    public function testName(): void
    {
        $this->assertEquals('storefront', $this->result->getName());
    }

    /**
     * @return void
     */
    public function testGetTests(): void
    {
        $this->assertCount(2, $this->result->getTests());
    }

    /**
     * @return void
     */
    public function testCount(): void
    {
        $this->assertEquals(2, $this->result->getTestCount());
    }

    /**
     * @return void
     */
    public function testFailureCount(): void
    {
        $this->assertEquals(1, $this->result->getFailureCount());
    }
}
