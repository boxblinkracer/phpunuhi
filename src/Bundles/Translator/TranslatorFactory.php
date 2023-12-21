<?php

namespace PHPUnuhi\Bundles\Translator;

use Exception;
use PHPUnuhi\Bundles\Translator\DeepL\DeeplTranslator;
use PHPUnuhi\Bundles\Translator\GoogleCloud\GoogleCloudTranslator;
use PHPUnuhi\Bundles\Translator\GoogleWeb\GoogleWebTranslator;
use PHPUnuhi\Bundles\Translator\OpenAI\OpenAITranslator;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Command\CommandOption;

class TranslatorFactory
{

    /**
     * @var TranslatorFactory
     */
    private static $instance;

    /**
     * @var TranslatorInterface[]
     */
    private $translators;


    /**
     * @return TranslatorFactory
     */
    public static function getInstance(): TranslatorFactory
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     *
     */
    private function __construct()
    {
        $this->resetStorages();
    }


    /**
     * @param TranslatorInterface $translator
     * @throws ConfigurationException
     * @return void
     */
    public function registerTranslator(TranslatorInterface $translator): void
    {
        $newName = $translator->getName();

        foreach ($this->translators as $existingTranslator) {
            if ($existingTranslator->getName() === $newName) {
                throw new ConfigurationException('Translator with name already registered: ' . $newName);
            }
        }

        $this->translators[] = $translator;
    }


    /**
     * Resets the registered translators to the default ones.
     * @return void
     */
    public function resetStorages(): void
    {
        $this->translators = [];

        $this->translators[] = new DeeplTranslator();
        $this->translators[] = new OpenAITranslator();
        $this->translators[] = new GoogleWebTranslator();
        $this->translators[] = new GoogleCloudTranslator();
    }


    /**
     * @return CommandOption[]
     */
    public function getAllOptions(): array
    {
        $options = [];

        foreach ($this->translators as $translator) {
            $options = array_merge($translator->getOptions(), $options);
        }

        return $options;
    }

    /**
     * @param string $service
     * @param array<mixed> $options
     * @throws Exception
     * @return TranslatorInterface
     */
    public function fromService(string $service, array $options): TranslatorInterface
    {
        if ($service === '' || $service === '0') {
            throw new Exception('No translator name provided.');
        }

        foreach ($this->translators as $translator) {
            if ($translator->getName() === $service) {
                # configure our translator with the
                # provided option values
                $translator->setOptionValues($options);

                return $translator;
            }
        }

        throw new Exception('No translator found with name: ' . $service);
    }
}
