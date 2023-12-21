<?php

namespace phpunit\Bundles\Translator;

use Exception;
use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeTranslator;
use PHPUnuhi\Bundles\Translator\TranslatorFactory;
use PHPUnuhi\Exceptions\ConfigurationException;

class TranslatorFactoryTest extends TestCase
{

    /**
     * @return void
     */
    protected function setUp(): void
    {
        TranslatorFactory::getInstance()->resetStorages();
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testGetCustomTranslator(): void
    {
        $custom = new FakeTranslator();
        TranslatorFactory::getInstance()->registerTranslator($custom);

        $translator = TranslatorFactory::getInstance()->fromService('fake', []);

        $this->assertSame($custom, $translator);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testUnknownServiceThrowsException(): void
    {
        $this->expectException(Exception::class);

        TranslatorFactory::getInstance()->fromService('unknown', []);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testNoServiceNameLeadsToException(): void
    {
        $this->expectException(Exception::class);

        TranslatorFactory::getInstance()->fromService('', []);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testDoubleRegistrationThrowsException(): void
    {
        $this->expectException(Exception::class);

        $custom = new FakeTranslator();

        TranslatorFactory::getInstance()->registerTranslator($custom);
        TranslatorFactory::getInstance()->registerTranslator($custom);
    }
}
