<?php

namespace PHPUnuhi\Bundles\Translator\GoogleCloud;

use Google\Cloud\Translate\V2\TranslateClient;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;

class GoogleCloudTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'googlecloud';
    }

    /**
     * @return CommandOption[]
     */
    public function getOptions(): array
    {
        return [
            new CommandOption('google-key', true),
        ];
    }

    /**
     * @param array<mixed> $options
     * @return void
     * @throws \Exception
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = (string)$options['google-key'];

        if (empty($this->apiKey)) {
            throw new \Exception('Please provide your API key for GoogleCloud');
        }
    }

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        $translate = new TranslateClient([
            'key' => $this->apiKey
        ]);

        $result = $translate->translate(
            $text,
            [
                'target' => $targetLocale
            ]
        );

        if (!isset($result['text'])) {
            return '';
        }

        return (string)$result['text'];
    }

}
