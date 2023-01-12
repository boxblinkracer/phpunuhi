<?php

namespace PHPUnuhi\Bundles\Translator;

use PHPUnuhi\Bundles\Translator\DeepL\DeeplTranslator;
use PHPUnuhi\Bundles\Translator\Fake\FakeTranslator;
use PHPUnuhi\Bundles\Translator\GoogleCloud\GoogleCloudTranslator;
use PHPUnuhi\Bundles\Translator\GoogleWeb\GoogleWebTranslator;
use PHPUnuhi\Bundles\Translator\OpenAI\OpenAITranslator;

class TranslatorFactory
{

    /**
     * @var TranslatorInterface[]
     */
    private $translators;


    public function __construct()
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
        if (empty($service)) {
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
