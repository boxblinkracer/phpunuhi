<?php

declare(strict_types=1);

namespace PHPUnuhi\Services\WordCounter;

class WordCounter
{
    public function getWordCount(string $text): int
    {
        return str_word_count($text);
    }
}
