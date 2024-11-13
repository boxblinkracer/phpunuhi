<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\WordCounter;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\WordCounter\WordCounter;

class WordCounterTest extends TestCase
{
    public function testCorrectTextCount(): void
    {
        $counter = new WordCounter();

        $words = $counter->getWordCount('this is a text!');

        $this->assertEquals(4, $words);
    }


    public function testOnlyRealWordsAreFound(): void
    {
        $counter = new WordCounter();

        $words = $counter->getWordCount('this is a text! ! -');

        $this->assertEquals(4, $words);
    }


    public function testCountOfEmptyString(): void
    {
        $counter = new WordCounter();

        $words = $counter->getWordCount('');

        $this->assertEquals(0, $words);
    }
}
