<?php

namespace phpunit\Components\Configuration;

use Exception;
use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeStorage;
use phpunit\Utils\Fakes\FakeStorageNoFilter;
use phpunit\Utils\Fakes\FakeXmlLoader;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;
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

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testMissingTranslationNodeThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $xml = '<phpunuhi>
</phpunuhi>';

        $this->loadXml($xml, new FakeStorage());
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testEmptyTranslationSetsThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $xml = '<phpunuhi><translations></translations></phpunuhi>';

        $this->loadXml($xml, new FakeStorage());
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testConfigurationLoaded(): void
    {
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

        $configuration = $this->loadXml($xml, new FakeStorage());

        $set1 = $configuration->getTranslationSets()[0];

        $this->assertCount(1, $configuration->getTranslationSets());
        $this->assertEquals('Storefront', $set1->getName());
        $this->assertEquals('fake', $set1->getFormat());

        $this->assertCount(2, $set1->getLocales());
        $this->assertEquals('en', $set1->getLocales()[0]->getName());
        $this->assertEquals('de', $set1->getLocales()[1]->getName());
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testFiltersForNotSupportedStorageThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $xml = '
        <phpunuhi>
            <translations>
                <set name="Storefront">
                    <format>
                        <fake/>
                    </format>
                     <filter>
                        <exclude>
                            <key>abc</key>
                        </exclude>
                    </filter>
                    <locales>
                        <locale name="en"></locale>
                    </locales>
                </set>
            </translations>
        </phpunuhi>
        ';

        $this->loadXml($xml, new FakeStorageNoFilter());
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testMissingStorageFormatThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $xml = '
        <phpunuhi>
            <translations>
                <set name="Storefront">
                    <locales>
                        <locale name="en"></locale>
                    </locales>
                </set>
            </translations>
        </phpunuhi>
        ';

        $this->loadXml($xml, new FakeStorageNoFilter());
    }

    /**
     * @param string $xml
     * @param StorageInterface $storage
     * @throws ConfigurationException
     * @return Configuration
     */
    private function loadXml(string $xml, StorageInterface $storage): Configuration
    {
        StorageFactory::getInstance()->resetStorages();
        StorageFactory::getInstance()->registerStorage($storage);

        $loader = new ConfigurationLoader(new FakeXmlLoader($xml));

        return $loader->load('not-existing.xml');
    }
}
