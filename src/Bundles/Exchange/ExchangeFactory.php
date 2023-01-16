<?php

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Bundles\Exchange\CSV\CSVExchange;
use PHPUnuhi\Bundles\Exchange\HTML\HTMLExchange;
use PHPUnuhi\Models\Command\CommandOption;

class ExchangeFactory
{

    /**
     * @var ExchangeInterface[]
     */
    private $exchangeServices;


    /**
     *
     */
    public function __construct()
    {
        $this->exchangeServices = [];

        $this->exchangeServices[] = new CSVExchange();
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
        if (empty($format)) {
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
