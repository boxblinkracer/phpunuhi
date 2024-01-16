<?php

namespace phpunit\Components\Repoter\Model;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Components\Reporter\Model\ReportResult;
use PHPUnuhi\Components\Reporter\Model\TestResult;
use PHPUnuhi\Components\Reporter\Model\TranslationSetResult;

class ReportResultTest extends TestCase
{


    /**
     * @var ReportResult
     */
    private $result;


    /**
     * @return void
     */
    public function setUp(): void
    {
        $suite1 = new TranslationSetResult('test');
        $suite1->addTestResult(
            new TestResult(
                '',
                '',
                '',
                55,
                '',
                '',
                true
            )
        );

        $suite2 = new TranslationSetResult('test2');

        $suite2->addTestResult(
            new TestResult(
                '',
                '',
                '',
                55,
                '',
                '',
                true
            )
        );

        $suite2->addTestResult(
            new TestResult(
                '',
                '',
                '',
                55,
                '',
                '',
                false
            )
        );

        $this->result = new ReportResult();
        $this->result->addTranslationSet($suite1);
        $this->result->addTranslationSet($suite2);
    }

    /**
     * @return void
     */
    public function testGetSuites(): void
    {
        $this->assertCount(2, $this->result->getSuites());
    }

    /**
     * @return void
     */
    public function testGetCount(): void
    {
        $this->assertEquals(3, $this->result->getTestCount());
    }

    /**
     * @return void
     */
    public function testFailureCount(): void
    {
        $this->assertEquals(1, $this->result->getFailureCount());
    }
}
