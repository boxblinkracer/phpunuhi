<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Traits\StringTrait;

class StringTraitTest extends TestCase
{
    use StringTrait;


    public function testContainsTrue(): void
    {
        $contains = $this->stringDoesContain('this is a text', 'text');

        $this->assertEquals(true, $contains);
    }


    public function testContainsFalse(): void
    {
        $contains = $this->stringDoesContain('this is a text', 'blub');

        $this->assertEquals(false, $contains);
    }


    public function testStringStartsWithTrue(): void
    {
        $contains = $this->stringDoesStartsWith('this is a text', 'this');

        $this->assertEquals(true, $contains);
    }


    public function testStringStartsWithFalse(): void
    {
        $contains = $this->stringDoesStartsWith('this is a text', 'is');

        $this->assertEquals(false, $contains);
    }


    public function testStringEndsWithTrue(): void
    {
        $contains = $this->stringDoesEndsWith('this is a text', 'text');

        $this->assertEquals(true, $contains);
    }


    public function testStringEndsWithFalse(): void
    {
        $contains = $this->stringDoesEndsWith('this is a text', 'this');

        $this->assertEquals(false, $contains);
    }


    public function testStringEndsWithEmptySearchString(): void
    {
        $contains = $this->stringDoesEndsWith('this is a text', '');

        $this->assertEquals(true, $contains);
    }


    public function testStringDoesContainInArray(): void
    {
        $contains = $this->stringDoesContainInArray('car', ['tshirt', 'car', 'jeans']);

        $this->assertEquals(true, $contains);
    }


    public function testStringDoesNotContainInEmptyArray(): void
    {
        $contains = $this->stringDoesContainInArray('this is a text', []);

        $this->assertEquals(false, $contains);
    }
}
