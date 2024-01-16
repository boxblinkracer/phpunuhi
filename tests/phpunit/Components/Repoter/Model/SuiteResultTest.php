<?php

namespace phpunit\Components\Repoter\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\Model\TranslationSetResult;

class SuiteResultTest extends TestCase
{


    /**
     * @var TranslationSetResult
     */
    private $result;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->result = new TranslationSetResult('storefront');

        $this->result->addTestResult(
            new TestResult(
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
            new TestResult(
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
