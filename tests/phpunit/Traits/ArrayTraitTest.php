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

        $flat = $this->getFlatArray($array);

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

        $dimensional = $this->getMultiDimensionalArray($array);

        $this->assertEquals($expected, $dimensional);
    }

}