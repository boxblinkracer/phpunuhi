<?php

namespace phpunit\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Traits\StringTrait;

class StringTraitTest extends TestCase
{
    use StringTrait;

    /**
     * @return void
     */
    public function testContainsTrue(): void
    {
        $contains = $this->stringDoesContain('this is a text', 'text');

        $this->assertEquals(true, $contains);
    }

    /**
     * @return void
     */
    public function testContainsFalse(): void
    {
        $contains = $this->stringDoesContain('this is a text', 'blub');

        $this->assertEquals(false, $contains);
    }

    /**
     * @return void
     */
    public function testStringStartsWithTrue(): void
    {
        $contains = $this->stringDoesStartsWith('this is a text', 'this');

        $this->assertEquals(true, $contains);
    }

    /**
     * @return void
     */
    public function testStringStartsWithFalse(): void
    {
        $contains = $this->stringDoesStartsWith('this is a text', 'is');

        $this->assertEquals(false, $contains);
    }

    /**
     * @return void
     */
    public function testStringEndsWithTrue(): void
    {
        $contains = $this->stringDoesEndsWith('this is a text', 'text');

        $this->assertEquals(true, $contains);
    }

    /**
     * @return void
     */
    public function testStringEndsWithFalse(): void
    {
        $contains = $this->stringDoesEndsWith('this is a text', 'this');

        $this->assertEquals(false, $contains);
    }

    /**
     * @return void
     */
    public function testStringEndsWithEmptySearchString(): void
    {
        $contains = $this->stringDoesEndsWith('this is a text', '');

        $this->assertEquals(true, $contains);
    }
}
