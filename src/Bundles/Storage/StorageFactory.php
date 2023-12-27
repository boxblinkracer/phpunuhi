<?php

namespace PHPUnuhi\Bundles\Storage;

use Exception;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;
use PHPUnuhi\Bundles\Storage\PO\PoStorage;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;
use PHPUnuhi\Bundles\Storage\YAML\YamlStorage;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\TranslationSet;

class StorageFactory
{

    /**
     * @var StorageFactory
     */
    private static $instance;

    /**
     * @var StorageInterface[]
     */
    private $storages;


    /**
     * @return StorageFactory
     */
    public static function getInstance(): StorageFactory
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
        $this->resetStorages();
    }

    /**
     * @param StorageInterface $storage
     * @throws ConfigurationException
     * @return void
     */
    public function registerStorage(StorageInterface $storage): void
    {
        $newName = $storage->getStorageName();

        foreach ($this->storages as $existingStorage) {
            if ($existingStorage->getStorageName() === $newName) {
                throw new ConfigurationException('Storage with name already registered: ' . $newName);
            }
        }

        $this->storages[] = $storage;
    }


    /**
     * Resets the registered storages to the default ones.
     * @return void
     */
    public function resetStorages(): void
    {
        $this->storages = [];

        $this->storages[] = new  JsonStorage();
        $this->storages[] = new  IniStorage();
        $this->storages[] = new  PhpStorage();
        $this->storages[] = new  PoStorage();
        $this->storages[] = new  YamlStorage();
        $this->storages[] = new  Shopware6Storage();
    }

    /**
     * @param TranslationSet $set
     * @throws ConfigurationException
     * @return StorageInterface
     */
    public function getStorage(TranslationSet $set): StorageInterface
    {
        $format = $set->getFormat();

        return $this->getStorageByFormat($format, $set);
    }

    /**
     * @param string $name
     * @param TranslationSet $set
     * @throws ConfigurationException
     * @return StorageInterface
     */
    public function getStorageByFormat(string $name, TranslationSet $set): StorageInterface
    {
        if (trim($name) === '') {
            throw new Exception('No name provided for the Storage');
        }

        foreach ($this->storages as $storage) {
            if ($storage->getStorageName() === $name) {
                $storage->configureStorage($set);

                return $storage;
            }
        }

        throw new ConfigurationException('No storage found for name: ' . $name);
    }
}
