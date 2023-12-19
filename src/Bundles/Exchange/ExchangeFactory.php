<?php

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Bundles\Exchange\CSV\CSVExchange;
use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriter;
use PHPUnuhi\Bundles\Exchange\HTML\HTMLExchange;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;
use PHPUnuhi\Bundles\Storage\PO\PoStorage;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\YAML\YamlStorage;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Command\CommandOption;

class ExchangeFactory
{

    /**
     * @var ExchangeFactory
     */
    private static $instance;

    /**
     * @var ExchangeInterface[]
     */
    private $exchangeServices;


    /**
     * @return ExchangeFactory
     */
    public static function getInstance(): ExchangeFactory
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
        $this->resetExchangeFormats();
    }


    /**
     * @param ExchangeInterface $exchangeFormat
     * @return void
     * @throws ConfigurationException
     */
    public function registerExchangeFormat(ExchangeInterface $exchangeFormat): void
    {
        $newName = $exchangeFormat->getName();

        foreach ($this->exchangeServices as $exchangeService) {
            if ($exchangeService->getName() === $newName) {
                throw new ConfigurationException('Exchange format with name already registered: ' . $newName);
            }
        }

        $this->exchangeServices[] = $exchangeFormat;
    }


    /**
     * Resets the registered exchange formats to the default ones.
     * @return void
     */
    public function resetExchangeFormats(): void
    {
        $this->exchangeServices = [];

        $this->exchangeServices[] = new CSVExchange(new CSVWriter());
        $this->exchangeServices[] = new HTMLExchange();
    }


    /**
     * @return CommandOption[]
     */
    public function getAllOptions(): array
    {
        $options = [];

        foreach ($this->exchangeServices as $exchangeService) {
            $options = array_merge($exchangeService->getOptions(), $options);
        }

        return $options;
    }

    /**
     * @param string $format
     * @param array<mixed> $options
     * @return ExchangeInterface
     * @throws \Exception
     */
    public function getExchange(string $format, array $options): ExchangeInterface
    {
        if ($format === '' || $format === '0') {
            throw new \Exception('No format name provided for the Exchange service');
        }

        foreach ($this->exchangeServices as $exchangeService) {

            if ($exchangeService->getName() === $format) {
                $exchangeService->setOptionValues($options);

                return $exchangeService;
            }
        }

        throw new \Exception('No Exchange service found for format: ' . $format);
    }

}
