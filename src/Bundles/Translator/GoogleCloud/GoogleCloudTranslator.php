<?php

namespace PHPUnuhi\Bundles\Translator\GoogleCloud;

use Exception;
use Google\Cloud\Translate\V2\TranslateClient;
use PHPUnuhi\Bundles\Translator\TranslatorInterface;
use PHPUnuhi\Models\Command\CommandOption;
use PHPUnuhi\Services\Placeholder\Placeholder;
use PHPUnuhi\Services\Placeholder\PlaceholderEncoder;

class GoogleCloudTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var PlaceholderEncoder
     */
    private $placeholderEncoder;

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'googlecloud';
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
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
     * @throws Exception
     * @return void
     */
    public function setOptionValues(array $options): void
    {
        $this->apiKey = isset($options['google-key']) ? (string)$options['google-key'] : '';

        $this->apiKey = trim($this->apiKey);

        if ($this->apiKey === '') {
            throw new Exception('Please provide your API key for GoogleCloud');
        }

        $this->placeholderEncoder = new PlaceholderEncoder();
    }

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param Placeholder[] $foundPlaceholders
     * @throws Exception
     * @return string
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $foundPlaceholders): string
    {
        $text = $this->placeholderEncoder->encode($text, $foundPlaceholders);

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

        $result = (string)$result['text'];

        if ($foundPlaceholders !== []) {
            # decode our string so that we have the original placeholder values again (%productName%)
            return $this->placeholderEncoder->decode($result, $foundPlaceholders);
        }

        return $result;
    }
}
