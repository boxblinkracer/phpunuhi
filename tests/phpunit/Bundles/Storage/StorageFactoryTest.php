<?php

namespace phpunit\Bundles\Storage;

use PHPUnit\Framework\TestCase;
use phpunit\Utils\Fakes\FakeStorage;
use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\TranslationSet;

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
            [],
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
}
