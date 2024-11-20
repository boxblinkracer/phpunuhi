<?php

declare(strict_types=1);

namespace PHPUnuhi\Configuration\Services;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandPrompt
{
    private SymfonyStyle $styler;

    public function __construct(SymfonyStyle $styler)
    {
        $this->styler = $styler;
    }

    public function askYesNoQuestion(string $question): bool
    {
        $answer = $this->styler->askQuestion(
            new Question($question, 'yes')
        );
        return preg_match('/^(:?yes|j|y|ja)$/i', $answer) === 1;
    }
}
