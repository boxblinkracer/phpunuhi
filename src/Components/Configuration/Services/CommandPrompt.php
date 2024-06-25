<?php

declare(strict_types=1);

namespace PHPUnuhi\Configuration\Services;

use ReflectionClass;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandPrompt
{
    /**
     * @var SymfonyStyle
     */
    private $styler;

    /**
     * If Interative is switched off,
     * then the answer should always be no
     *
     * @var bool
     */
    private $defaultYes;

    /**
     * @param SymfonyStyle $styler
     */
    public function __construct(SymfonyStyle $styler)
    {
        $this->styler = $styler;
        $this->defaultYes = $this->isInteractive($styler);
    }

    /**
     * @param string $question
     * @return bool
     */
    public function askYesNoQuestion(string $question): bool
    {
        $answer = $this->styler->askQuestion(
            new Question($question, $this->defaultYes ? 'yes' : 'no')
        );
        return preg_match('/^(:?yes|j|y|ja)$/i', $answer) === 1;
    }

    private function isInteractive(SymfonyStyle $style): bool
    {
        $property = (new ReflectionClass($style))->getProperty('input');
        $property->setAccessible(true);
        $input = $property->getValue($style);
        $property->setAccessible(false);

        return $input->isInteractive();
    }
}
