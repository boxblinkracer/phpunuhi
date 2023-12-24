<?php

namespace phpunit\Components\Configuration;

use Exception;
use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeStorage;
use phpunit\Utils\Fakes\FakeXmlLoader;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;

class ConfigurationLoaderTest extends TestCase
{

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testNonExistingFileThrowsException(): void
    {
        $this->expectException(Exception::class);

        $loader = new ConfigurationLoader(new XmlLoader());
        $loader->load('not-existing.xml');
    }

    public function test(): void
    {
        StorageFactory::getInstance()->resetStorages();
        StorageFactory::getInstance()->registerStorage(new FakeStorage());

        $xml = '
        <phpunuhi>
            <translations>
                <set name="Storefront">
                    <format>
                        <fake/>
                    </format>
                    <locales>
                        <locale name="en"></locale>
                        <locale name="de"></locale>
                    </locales>
                </set>
            </translations>
        </phpunuhi>
        ';

        $loader = new ConfigurationLoader(new FakeXmlLoader($xml));

        $configuration = $loader->load('not-existing.xml');

        $set1 = $configuration->getTranslationSets()[0];

        $this->assertCount(1, $configuration->getTranslationSets());
        $this->assertEquals('Storefront', $set1->getName());
        $this->assertEquals('fake', $set1->getFormat());

        $this->assertCount(2, $set1->getLocales());
        $this->assertEquals('en', $set1->getLocales()[0]->getName());
        $this->assertEquals('de', $set1->getLocales()[1]->getName());
    }
}
