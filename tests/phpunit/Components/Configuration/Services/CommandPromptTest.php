<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration\Services;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Configuration\Services\CommandPrompt;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandPromptTest extends TestCase
{
    /**
     * @dataProvider userAnswer
     */
    public function testAskYesNoQuestion(object $data): void
    {
        $styler = $this->createMock(SymfonyStyle::class);
        $styler->expects($this->once())
            ->method('askQuestion')
            ->willReturn($data->answer); // @phpstan-ignore property.notFound

        $commandPrompt = new CommandPrompt($styler);
        $actual = $commandPrompt->askYesNoQuestion('Are you sure?');

        $this->assertEquals($data->expect, $actual); // @phpstan-ignore property.notFound
    }

    /**
     * @return array<string, object[]>
     */
    public function userAnswer(): array
    {
        return [
            'yes' => [(object) ['answer' => 'yes', 'expect' => true]],
            'j' => [(object) ['answer' => 'j', 'expect' => true]],
            'y' => [(object) ['answer' => 'y', 'expect' => true]],
            'jA' => [(object) ['answer' => 'jA', 'expect' => true]],
            'nein' => [(object) ['answer' => 'nein', 'expect' => false]],
            'no' => [(object) ['answer' => 'no', 'expect' => false]],
            'n' => [(object) ['answer' => 'n', 'expect' => false]],
            'noyes' => [(object) ['answer' => 'noyes', 'expect' => false]],
        ];
    }
}
