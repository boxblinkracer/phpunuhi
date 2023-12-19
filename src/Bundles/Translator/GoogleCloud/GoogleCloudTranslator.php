<?php

namespace PHPUnuhi\Bundles\Translator\GoogleCloud;

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

        if ($this->apiKey === '' || $this->apiKey === '0') {
            throw new \Exception('Please provide your API key for GoogleCloud');
        }

        $this->placeholderEncoder = new PlaceholderEncoder();
    }

    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param Placeholder[] $foundPlaceholders
     * @return string
     * @throws \Exception
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
            $result = $this->placeholderEncoder->decode($result, $foundPlaceholders);
        }

        return $result;
    }

}
