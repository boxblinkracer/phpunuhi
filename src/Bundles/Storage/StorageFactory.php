<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage;

use Exception;
use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;
use PHPUnuhi\Bundles\Storage\PO\PoStorage;
use PHPUnuhi\Bundles\Storage\RESX\ResxStorage;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;
use PHPUnuhi\Bundles\Storage\Strings\StringsStorage;
use PHPUnuhi\Bundles\Storage\YAML\YamlStorage;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\TranslationSet;

class StorageFactory
{
    private static ?\PHPUnuhi\Bundles\Storage\StorageFactory $instance = null;

    /**
     * @var StorageInterface[]
     */
    private array $storages;



    public static function getInstance(): StorageFactory
    {
        if (!self::$instance instanceof \PHPUnuhi\Bundles\Storage\StorageFactory) {
            self::$instance = new self();
        }

        return self::$instance;
    }



    private function __construct()
    {
        $this->resetStorages();
    }


    /**
     * @throws ConfigurationException
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
     */
    public function resetStorages(): void
    {
        $this->storages = [];

        $this->storages[] = new JsonStorage();
        $this->storages[] = new IniStorage();
        $this->storages[] = new PhpStorage();
        $this->storages[] = new PoStorage();
        $this->storages[] = new YamlStorage();
        $this->storages[] = new ResxStorage();
        $this->storages[] = new StringsStorage();
        $this->storages[] = new Shopware6Storage();
    }

    /**
     * @return StorageInterface[]
     */
    public function getStorages(): array
    {
        return $this->storages;
    }

    /**
     * @throws ConfigurationException
     */
    public function getStorage(TranslationSet $set): StorageInterface
    {
        $format = $set->getFormat();

        return $this->getStorageByFormat($format, $set);
    }

    /**
     * @throws ConfigurationException
     */
    public function getStorageByFormat(string $name, TranslationSet $set): StorageInterface
    {
        if (trim($name) === '') {
            throw new Exception('No name provided for the Storage');
        }

        foreach ($this->storages as $storage) {
            if (strtolower($storage->getStorageName()) === strtolower($name)) {
                $storage->configureStorage($set);

                return $storage;
            }
        }

        throw new ConfigurationException('No storage found for name: ' . $name);
    }
}
