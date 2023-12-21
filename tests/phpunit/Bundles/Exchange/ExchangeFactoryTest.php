<?php

namespace phpunit\Bundles\Exchange;

use Exception;
use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeExchangeFormat;
use phpunit\Utils\Fakes\FakeStorage;
use PHPUnuhi\Bundles\Exchange\ExchangeFactory;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\TranslationSet;

class ExchangeFactoryTest extends TestCase
{

    /**
     * @return void
     */
    protected function setUp(): void
    {
        ExchangeFactory::getInstance()->resetExchangeFormats();
    }


    /**
     * @return void
     * @throws ConfigurationException
     */
    public function testGetCustomExchangeFormat(): void
    {
        $custom = new FakeExchangeFormat();
        ExchangeFactory::getInstance()->registerExchangeFormat($custom);

        $exchange = ExchangeFactory::getInstance()->getExchange('fake', []);

        $this->assertSame($custom, $exchange);
    }

    /**
     * @return void
     * @throws ConfigurationException
     */
    public function testDoubleRegistrationThrowsException(): void
    {
        $this->expectException(Exception::class);

        $custom = new FakeExchangeFormat();

        ExchangeFactory::getInstance()->registerExchangeFormat($custom);
        ExchangeFactory::getInstance()->registerExchangeFormat($custom);
    }

    /**
     * @return void
     * @throws ConfigurationException
     */
    public function testUnknownFormatThrowsException(): void
    {
        $this->expectException(Exception::class);

        ExchangeFactory::getInstance()->getExchange('unknown', []);
    }

    /**
     * @return void
     * @throws ConfigurationException
     */
    public function testEmptyFormatThrowsException(): void
    {
        $this->expectException(Exception::class);

        ExchangeFactory::getInstance()->getExchange('', []);
    }

}
