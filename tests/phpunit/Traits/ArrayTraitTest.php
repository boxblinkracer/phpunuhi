<?php

namespace phpunit\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Filter;
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

    public function testGetLineNumbers()
    {
        $array = [
            'title' => 'Title',
            'content' => [
                'headline' => 'A content',
                'description' => 'this is my description'
            ]
        ];

        $expected = [
            'title' => 1,
            'content.headline' => 3,
            'content.description' => 4,
            '__LINE_NUMBER__' => 4,
        ];

        $flat = $this->getLineNumbers($array, '.');

        $this->assertEquals($expected, $flat);
    }

    public function testGetLineNumbersWithOffset()
    {
        $array = [
            'title' => 'Title',
            'content' => [
                'headline' => 'A content',
                'description' => 'this is my description'
            ]
        ];

        $expected = [
            'title' => 2,
            'content.headline' => 4,
            'content.description' => 5,
            '__LINE_NUMBER__' => 5,
        ];

        $flat = $this->getLineNumbers($array, '.', '', 1);

        $this->assertEquals($expected, $flat);
    }

    public function testGetLineNumbersWithClosingBrackets()
    {
        $array = [
            'title' => 'Title',
            'content' => [
                'headline' => 'A content',
                'description' => 'this is my description'
            ],
            'content2' => [
                'headline' => 'A content',
                'description' => 'this is my description'
            ],
        ];

        $expected = [
            'title' => 1,
            'content.headline' => 3,
            'content.description' => 4,
            'content2.headline' => 7,
            'content2.description' => 8,
            '__LINE_NUMBER__' => 9,
        ];

        $flat = $this->getLineNumbers($array, '.', '', 0, true);

        $this->assertEquals($expected, $flat);
    }

    public function testGetFilteredResult()
    {
        $array = [
            'title' => 'Title',
            'content.headline' => 'A content',
            'content.description' => 'this is my description',
            'content2.headline' => 'A content',
            'content2.description' => 'this is my description',
        ];

        $filter = new Filter();
        $filter->addExcludeKey('headline');

        [$result, $filtered] = $this->getFilteredResult($array, $filter);

        $this->assertEquals([
            'title' => 'Title',
            'content.description' => 'this is my description',
            'content2.description' => 'this is my description',
        ], $result);
        $this->assertEquals([
            'content.headline' => 'A content',
            'content2.headline' => 'A content',
        ], $filtered);
    }
}