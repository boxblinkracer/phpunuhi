<?php

namespace phpunit\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Traits\BinaryTrait;

class BinaryTraitTest extends TestCase
{

    use BinaryTrait;

    /**
     * @return void
     */
    public function testBinaryToString(): void
    {
        $hex = $this->stringToBinary('0d1eeedd6d22436385580e2ff42431b9');
        $string = $this->binaryToString($hex);

        $this->assertEquals('0d1eeedd6d22436385580e2ff42431b9', $string);
    }

    /**
     * @return void
     */
    public function testStringToBinary(): void
    {
        $binary = $this->stringToBinary('0d1eeedd6d22436385580e2ff42431b9');

        $hex = $this->binaryToString($binary);

        $this->assertEquals('0d1eeedd6d22436385580e2ff42431b9', $hex);
    }

    /**
     * @return void
     */
    public function testStringToBinaryWithEmpty(): void
    {
        $hex = $this->stringToBinary('');

        $this->assertEquals('', $hex);
    }

    /**
     * @return void
     */
    public function testIsBinaryTrue(): void
    {
        $binary = $this->stringToBinary('0d1eeedd6d22436385580e2ff42431b9');

        $isBinary = $this->isBinary($binary);

        $this->assertTrue($isBinary);
    }

    /**
     * @return void
     */
    public function testIsBinaryFalse(): void
    {
        $isBinary = $this->isBinary('');

        $this->assertFalse($isBinary);
    }

}