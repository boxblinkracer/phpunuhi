<?php

namespace PHPUnuhi\Tests\Bundles\Storage;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Tests\Utils\Fakes\FakeStorage;

class StorageFactoryTest extends TestCase
{

    /**
     * @var TranslationSet
     */
    private $set;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->set = new TranslationSet(
            '',
            'fake',
            new Protection(),
            [],
            new Filter(),
            [],
            new CaseStyleSetting([], []),
            []
        );

        StorageFactory::getInstance()->resetStorages();
    }


    /**
     * This test verifies that we can successfully register a new storage
     * and that it will be returned correctly when accessing it.
     * @throws ConfigurationException
     * @return void
     */
    public function testGetCustomStorage(): void
    {
        $fakeStorage = new FakeStorage();
        StorageFactory::getInstance()->registerStorage($fakeStorage);

        $storage = StorageFactory::getInstance()->getStorageByFormat('fake', $this->set);

        $this->assertSame($fakeStorage, $storage);
    }

    /**
     * This test verifies that we can successfully register a new storage
     * and that it will be returned for the provided set correctly.
     * @throws ConfigurationException
     * @return void
     */
    public function testGetCustomStorageBySet(): void
    {
        $fakeStorage = new FakeStorage();
        StorageFactory::getInstance()->registerStorage($fakeStorage);

        $storage = StorageFactory::getInstance()->getStorage($this->set);

        $this->assertSame($fakeStorage, $storage);
    }

    /**
     * @testWith [""]
     *           [" "]
     *
     * @param string $emptyFormat
     * @throws ConfigurationException
     * @return void
     */
    public function testEmptyStorageFormatThrowsException(string $emptyFormat): void
    {
        $this->expectException(Exception::class);

        StorageFactory::getInstance()->getStorageByFormat($emptyFormat, $this->set);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testUnknownStorageFormatThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        StorageFactory::getInstance()->getStorageByFormat('unknown', $this->set);
    }

    /**
     * @throws ConfigurationException
     * @return void
     */
    public function testDuplicateRegistrationThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        StorageFactory::getInstance()->registerStorage(new FakeStorage());
        StorageFactory::getInstance()->registerStorage(new FakeStorage());
    }
}
