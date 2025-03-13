<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange;

use Exception;
use PHPUnuhi\Bundles\Exchange\CSV\CSVExchange;
use PHPUnuhi\Bundles\Exchange\CSV\Services\CSVWriter;
use PHPUnuhi\Bundles\Exchange\HTML\HTMLExchange;
use PHPUnuhi\Bundles\Exchange\JSON\JsonExchange;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Command\CommandOption;

class ExchangeFactory
{
    private static ?ExchangeFactory $instance = null;

    /**
     * @var ExchangeInterface[]
     */
    private array $exchangeServices;


    public static function getInstance(): ExchangeFactory
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    private function __construct()
    {
        $this->resetExchangeFormats();
    }


    /**
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
     * @return ExchangeInterface[]
     */
    public function getExchangeFormats(): array
    {
        return $this->exchangeServices;
    }

    /**
     * Resets the registered exchange formats to the default ones.
     */
    public function resetExchangeFormats(): void
    {
        $this->exchangeServices = [];

        $this->exchangeServices[] = new CSVExchange(new CSVWriter());
        $this->exchangeServices[] = new HTMLExchange();
        $this->exchangeServices[] = new JsonExchange();
    }


    /**
     * @return CommandOption[]
     */
    public function getAllOptions(): array
    {
        $options = [];

        foreach ($this->exchangeServices as $exchangeService) {
            $options[] = $exchangeService->getOptions();
        }

        return array_merge([], ...array_reverse($options));
    }

    /**
     * @param array<string|bool|int|float|array|null> $options
     * @throws Exception
     */
    public function getExchange(string $format, array $options): ExchangeInterface
    {
        if ($format === '') {
            throw new Exception('No format name provided for the Exchange service');
        }

        foreach ($this->exchangeServices as $exchangeService) {
            if ($exchangeService->getName() === $format) {
                $exchangeService->setOptionValues($options);

                return $exchangeService;
            }
        }

        throw new Exception('No Exchange service found for format: ' . $format);
    }
}
