<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Components\Configuration;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Configuration\ConfigurationLoader;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Services\Loaders\Xml\XmlLoader;
use PHPUnuhi\Tests\Utils\Fakes\FakeStorage;
use PHPUnuhi\Tests\Utils\Fakes\FakeStorageNoFilter;
use PHPUnuhi\Tests\Utils\Fakes\FakeXmlLoader;

class ConfigurationLoaderTest extends TestCase
{
    /**
     * @throws ConfigurationException
     */
    public function testNonExistingFileThrowsException(): void
    {
        $this->expectException(Exception::class);

        $loader = new ConfigurationLoader(new XmlLoader());
        $loader->load('not-existing.xml');
    }

    /**
     * @throws ConfigurationException
     */
    public function testMissingTranslationNodeThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $xml = '<phpunuhi><unknown></unknown></phpunuhi>';

        $this->loadXml($xml, new FakeStorage());
    }

    /**
     * @throws ConfigurationException
     */
    public function testEmptyTranslationSetsThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $xml = '<phpunuhi><translations></translations></phpunuhi>';

        $this->loadXml($xml, new FakeStorage());
    }

    /**
     * @throws ConfigurationException
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
     * @throws ConfigurationException
     */
    public function testLoadPHPEnvironment(): void
    {
        $xml = '
        <phpunuhi>
            <php>
                <env name="DB_HOST" value="127.0.0.1"/>
            </php>
            <translations>
                <set name="Storefront">
                    <format>
                        <fake/>
                    </format>
                    <locales>
                        <locale name="en"></locale>
                    </locales>
                </set>
            </translations>
        </phpunuhi>
        ';

        $this->loadXml($xml, new FakeStorage());

        $phpEnvDBHost = getenv('DB_HOST');

        $this->assertEquals('127.0.0.1', $phpEnvDBHost);
    }

    /**
     * @throws ConfigurationException
     */
    public function testMissingPhpEnvNodeIsSkipped(): void
    {
        $xml = '
        <phpunuhi>
            <php>
            </php>
            <translations>
                <set name="Storefront">
                    <format>
                        <fake/>
                    </format>
                    <locales>
                        <locale name="en"></locale>
                    </locales>
                </set>
            </translations>
        </phpunuhi>
        ';

        $configuration = $this->loadXml($xml, new FakeStorage());

        # just verify configs are loaded
        $this->assertCount(1, $configuration->getTranslationSets());
    }

    /**
     * @throws ConfigurationException
     */
    private function loadXml(string $xml, StorageInterface $storage): Configuration
    {
        StorageFactory::getInstance()->resetStorages();
        StorageFactory::getInstance()->registerStorage($storage);

        $loader = new ConfigurationLoader(new FakeXmlLoader($xml));

        return $loader->load('not-existing.xml');
    }
}
