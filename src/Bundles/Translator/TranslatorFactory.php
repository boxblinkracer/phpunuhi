<?php

declare(strict_types=1);

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
    private static ?\PHPUnuhi\Bundles\Translator\TranslatorFactory $instance = null;

    /**
     * @var TranslatorInterface[]
     */
    private array $translators;



    public static function getInstance(): TranslatorFactory
    {
        if (!self::$instance instanceof \PHPUnuhi\Bundles\Translator\TranslatorFactory) {
            self::$instance = new self();
        }

        return self::$instance;
    }



    private function __construct()
    {
        $this->resetTranslators();
    }


    /**
     * @throws ConfigurationException
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
     * @return TranslatorInterface[]
     */
    public function getTranslators(): array
    {
        return $this->translators;
    }

    /**
     * Resets the registered translators to the default ones.
     */
    public function resetTranslators(): void
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
     * @param array<mixed> $options
     * @throws Exception
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
