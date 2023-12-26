<?php

namespace PHPUnuhi\Tests\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Translation;

class TranslationTest extends TestCase
{

    /**
     * @return void
     */
    public function testKey(): void
    {
        $translation = new Translation('title', 'Titel', '');

        $this->assertEquals('title', $translation->getID());
    }

    /**
     * @return void
     */
    public function testValue(): void
    {
        $translation = new Translation('title', 'Titel', '');

        $this->assertEquals('Titel', $translation->getValue());
    }

    /**
     * @return void
     */
    public function testIsEmptyWithSpaces(): void
    {
        $translation = new Translation('title', '   ', '');

        $this->assertEquals(true, $translation->isEmpty());
    }

    /**
     * @return void
     */
    public function testSetValue(): void
    {
        $translation = new Translation('title', '   ', '');
        $translation->setValue('abc');

        $this->assertEquals('abc', $translation->getValue());
    }

    /**
     * @return void
     */
    public function testGroup(): void
    {
        $translation = new Translation('title', '   ', 'product-123');
        $translation->setValue('abc');

        $this->assertEquals('product-123', $translation->getGroup());
    }

    /**
     * @testWith  [ 0, "title" ]
     *            [ 0, "title_title" ]
     *            [ 1, "title.title" ]
     *            [ 2, "title.title.title" ]
     *
     * @param int $expected
     * @param string $key
     * @return void
     */
    public function testGetLevel(int $expected, string $key): void
    {
        $translation = new Translation($key, '', '');

        $level = $translation->getLevel('.');

        $this->assertEquals($expected, $level);
    }

    /**
     * @return void
     */
    public function testGetLevelWithEmptyDelimiter(): void
    {
        $translation = new Translation('title.title2', '', '');

        $level = $translation->getLevel('');

        $this->assertEquals(0, $level);
    }
}
