<?php

namespace PHPUnuhi\Services\WordCounter;

class WordCounter
{

    /**
     * @param string $text
     * @return int
     */
    public function getWordCount(string $text): int
    {
        return str_word_count($text);
    }
}
