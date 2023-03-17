<?php

namespace phpunit\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Traits\ArrayTrait;

class ArrayTraitTest extends TestCase
{

    use ArrayTrait;

    /**
     * @return void
     */
    public function testGetFlatArray()
    {
        $array = [
            'title' => 'Title',
            'content' => [
                'headline' => 'A content',
                'description' => 'this is my description'
            ]
        ];

        $expected = [
            'title' => 'Title',
            'content.headline' => 'A content',
            'content.description' => 'this is my description',
        ];

        $flat = $this->getFlatArray($array, '.');

        $this->assertEquals($expected, $flat);
    }

    /**
     * @return void
     */
    public function testGetMultiDimensionalArray()
    {
        $array = [
            'title' => 'Title',
            'content.headline' => 'A content',
            'content.description' => 'this is my description',
        ];

        $expected = [
            'title' => 'Title',
            'content' => [
                'headline' => 'A content',
                'description' => 'this is my description'
            ]
        ];

        $dimensional = $this->getMultiDimensionalArray($array, '.');

        $this->assertEquals($expected, $dimensional);
    }

    /**
     * @return void
     */
    public function testGetMultiDimensionalWithDifferentDelimiter()
    {
        $array = [
            'card.title' => 'Title',
            'card2,title' => 'Title 2',
        ];

        $expected = [
            'card.title' => 'Title',
            'card2' => [
                'title' => 'Title 2',
            ]
        ];

        $dimensional = $this->getMultiDimensionalArray($array, ',');

        $this->assertEquals($expected, $dimensional);
    }

    /**
     * @return void
     */
    public function testGetMultiDimensionalWithEmptyDelimiter()
    {
        $array = [
            'card.title' => 'Title',
        ];

        $expected = [
            'card.title' => 'Title',
        ];

        $dimensional = $this->getMultiDimensionalArray($array, '');

        $this->assertEquals($expected, $dimensional);
    }

    /**
     * @return void
     */
    public function testGetMultiDimensionalWith2NestedLevels()
    {
        $array = [
            'sub.sub2' => 'Title',
            'sub.subsub.test' => 'Title',
            'title' => 'Title',
        ];

        $expected = [
            'sub' => [
                'sub2' => 'Title',
                'subsub' => [
                    'test' => 'Title'
                ]
            ],
            'title' => 'Title',
        ];

        $dimensional = $this->getMultiDimensionalArray($array, '.');

        $this->assertEquals($expected, $dimensional);
    }


}