<?php

namespace PHPUnuhi\Bundles\Spelling\Aspell;

use PHPUnuhi\Bundles\Spelling\Result\MisspelledWord;
use PHPUnuhi\Bundles\Spelling\Result\SpellingValidationResult;
use PHPUnuhi\Bundles\Spelling\SpellCheckerInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Models\Text\Text;
use PHPUnuhi\Services\Process\OSProcess;
use PHPUnuhi\Traits\StringTrait;
use RuntimeException;
use Throwable;

class AspellSpellChecker implements SpellCheckerInterface
{
    use StringTrait;

    /**
     * @var string
     */
    private $binary = 'aspell';

    /**
     * @var OSProcess
     */
    private $process;


    /**
     *
     */
    public function __construct()
    {
        $this->process = new OSProcess();
    }

    public function getName(): string
    {
        return "aspell";
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [
            new CommandOption('binary', true)
        ];
    }

    /**
     * @param array<mixed> $options
     * @return void
     */
    public function setOptionValues(array $options): void
    {
        if (isset($options['binary'])) {
            $this->binary = $options['binary'];
        }
    }


    /**
     * @return string[]
     */
    public function getAvailableDictionaries(): array
    {
        $this->checkIfInstalled();

        $cmdResult = $this->process->execute($this->binary . ' dump dicts');

        if (!$cmdResult->isSuccess()) {
            throw new RuntimeException($cmdResult->getErrorOutput());
        }

        return $cmdResult->getOutputLines();
    }

    public function validate(Text $text, string $locale): SpellingValidationResult
    {
        $this->checkIfInstalled();

        $command = 'echo ' . escapeshellarg($text->getEncodedText()) . ' | ' . $this->binary . ' -d ' . escapeshellarg($locale) . ' list';

        $cmdResult = $this->process->execute($command);

        # if we do not get an output, then we have no misspelled words
        $isSpellingValid = count($cmdResult->getOutputLines()) === 0;

        $misspelledWords = [];

        if (!$isSpellingValid) {
            foreach ($cmdResult->getOutputLines() as $misspelledWord) {
                $cmd = 'echo ' . escapeshellarg($misspelledWord) . ' | ' . $this->binary . ' -a -d ' . escapeshellarg($locale);

                $suggestOutput = $this->process->execute($cmd);

                $suggestions = [];

                foreach ($suggestOutput->getOutputLines() as $line) {
                    if (strpos($line, '&') === 0) {
                        $parts = explode(": ", $line);
                        if (isset($parts[1])) {
                            $suggestions = array_map('trim', explode(", ", $parts[1]));
                        }
                    }
                }

                $misspelledWords[] = new MisspelledWord($misspelledWord, $suggestions);
            }
        }

        return new SpellingValidationResult($isSpellingValid, $locale, '', $misspelledWords);
    }

    public function fixSpelling(Text $text, string $locale): string
    {
        $this->checkIfInstalled();

        $command = 'echo ' . escapeshellarg($text->getEncodedText()) . ' | aspell -a -d ' . escapeshellarg($locale);

        $cmdResult = $this->process->execute($command);

        $correctedText = $text->getEncodedText();

        # aspell doesn't return a corrected text, so we have to do it manually
        # by replacing the misspelled words with the first suggestion
        foreach ($cmdResult->getOutputLines() as $line) {
            if (strpos($line, '&') === 0) {
                $parts = explode(" ", $line);
                $misspelledWord = $parts[1];
                $suggestionsPart = explode(": ", $line);
                $suggestions = isset($suggestionsPart[1]) ? explode(", ", $suggestionsPart[1]) : [];

                if ($suggestions !== []) {
                    $correctedText = str_replace($misspelledWord, $suggestions[0], $correctedText);
                }
            }
        }

        return $correctedText;
    }


    private function checkIfInstalled(): void
    {
        try {
            $result = $this->process->execute($this->binary . " -v");

            if (!$result->isSuccess()) {
                throw new RuntimeException($result->getErrorOutput());
            }
        } catch (Throwable $ex) {
            throw new RuntimeException('Aspell binary not found.

To troubleshoot, try the following:
' .
            "1. Run 'aspell -v' to check if Aspell is installed and accessible.\n" .
            "2. If not installed, use one of these commands to install it:\n" .
            "   - macOS: 'brew install aspell'\n" .
            "   - Ubuntu/Debian: 'sudo apt-get install aspell'\n\n" .
            "Alternatively, consult online resources for installation instructions specific to your system.\n\n" .
            $ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
