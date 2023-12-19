<?php

namespace PHPUnuhi\Bundles\Translator;

use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;
use PHPUnuhi\Bundles\Storage\PO\PoStorage;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\YAML\YamlStorage;
use PHPUnuhi\Bundles\Translator\DeepL\DeeplTranslator;
use PHPUnuhi\Bundles\Translator\Fake\FakeTranslator;
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
     * @return void
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
     * Resets the registered translators to the default ones.
     * @return void
     */
    public function resetStorages(): void
    {
        $this->translators = [];

        $this->translators[] = new FakeTranslator();
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
     * @return TranslatorInterface
     * @throws \Exception
     */
    public function fromService(string $service, array $options): TranslatorInterface
    {
        if ($service === '' || $service === '0') {
            throw new \Exception('No translator name provided.');
        }

        foreach ($this->translators as $translator) {

            if ($translator->getName() === $service) {
                # configure our translator with the
                # provided option values
                $translator->setOptionValues($options);

                return $translator;
            }
        }

        throw new \Exception('No translator found with name: ' . $service);
    }

}
