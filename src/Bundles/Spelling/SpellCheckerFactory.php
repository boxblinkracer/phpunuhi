<?php

namespace PHPUnuhi\Bundles\Spelling;


use PHPUnuhi\Bundles\Spelling\Fake\FakeSpellChecker;
use PHPUnuhi\Bundles\Spelling\OpenAI\OpenAISpellChecker;
use PHPUnuhi\Bundles\Spelling\PHP\PhpSpellChecker;

class SpellCheckerFactory
{

    /**
     * @param string $service
     * @param string $apiKey
     * @return SpellCheckerInterface
     * @throws \Exception
     */
    public static function fromService(string $service, string $apiKey): SpellCheckerInterface
    {
        switch (strtolower($service)) {

            case 'fake':
                return new FakeSpellChecker();

            case 'openai':
                return new OpenAISpellChecker($apiKey);

            default:
                throw new \Exception('Translator service ' . $service . ' not found in PHPUnuhi');
        }
    }

}
