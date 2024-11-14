<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Spelling;

use Exception;
use PHPUnuhi\Bundles\Spelling\Aspell\AspellSpellChecker;
use PHPUnuhi\Bundles\Spelling\OpenAI\OpenAISpellChecker;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Command\CommandOption;

class SpellCheckerFactory
{
    private static ?\PHPUnuhi\Bundles\Spelling\SpellCheckerFactory $instance = null;

    /**
     * @var SpellCheckerInterface[]
     */
    private array $spellCheckers;


    public static function getInstance(): SpellCheckerFactory
    {
        if (!self::$instance instanceof \PHPUnuhi\Bundles\Spelling\SpellCheckerFactory) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    private function __construct()
    {
        $this->resetSpellCheckers();
    }


    /**
     * @throws ConfigurationException
     */
    public function registerSpellChecker(SpellCheckerInterface $spellChecker): void
    {
        $newName = $spellChecker->getName();

        foreach ($this->spellCheckers as $existingTranslator) {
            if ($existingTranslator->getName() === $newName) {
                throw new ConfigurationException('Spellchecker with name already registered: ' . $newName);
            }
        }

        $this->spellCheckers[] = $spellChecker;
    }

    /**
     * @return SpellCheckerInterface[]
     */
    public function getSpellCheckers(): array
    {
        return $this->spellCheckers;
    }

    /**
     * Resets the registered translators to the default ones.
     */
    public function resetSpellCheckers(): void
    {
        $this->spellCheckers = [];

        $this->spellCheckers[] = new AspellSpellChecker();
        $this->spellCheckers[] = new OpenAISpellChecker();
    }

    /**
     * @return CommandOption[]
     */
    public function getAllOptions(): array
    {
        $options = [];

        foreach ($this->spellCheckers as $spellChecker) {
            $options = array_merge($spellChecker->getOptions(), $options);
        }

        return $options;
    }

    /**
     * @param array<mixed> $options
     * @throws Exception
     */
    public function fromService(string $service, array $options): SpellCheckerInterface
    {
        if ($service === '') {
            throw new Exception('No spell checker name provided.');
        }

        foreach ($this->spellCheckers as $spellChecker) {
            if ($spellChecker->getName() === $service) {
                # configure our spellchecker with the
                # provided option values
                $spellChecker->setOptionValues($options);

                return $spellChecker;
            }
        }

        throw new Exception('No spell checker found with name: ' . $service);
    }
}
