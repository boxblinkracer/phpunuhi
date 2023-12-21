<?php

namespace phpunit\Services\WordCounter;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\WordCounter\WordCounter;

class WordCounterTest extends TestCase
{

    /**
     * @return void
     */
    public function testCorrectTextCount(): void
    {
        $counter = new WordCounter();

        $words = $counter->getWordCount('this is a text!');

        $this->assertEquals(4, $words);
    }

    /**
     * @return void
     */
    public function testOnlyRealWordsAreFound(): void
    {
        $counter = new WordCounter();

        $words = $counter->getWordCount('this is a text! ! -');

        $this->assertEquals(4, $words);
    }

    /**
     * @return void
     */
    public function testCountOfEmptyString(): void
    {
        $counter = new WordCounter();

        $words = $counter->getWordCount('');

        $this->assertEquals(0, $words);
    }
}
