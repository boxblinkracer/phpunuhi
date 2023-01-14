<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;
use PHPUnuhi\Bundles\Storage\PHP\PhpStorage;
use PHPUnuhi\Bundles\Storage\Shopware6\Shopware6Storage;


class StorageFactory
{

    /**
     * @param string $format
     * @param int $jsonIndent
     * @param bool $sortStorage
     * @return StorageInterface
     * @throws \Exception
     */
    public static function getStorage(string $format, int $jsonIndent, bool $sortStorage): StorageInterface
    {
        switch (strtolower($format)) {
            case StorageFormat::JSON:
                return new JsonStorage($jsonIndent, $sortStorage);

            case StorageFormat::INI:
                return new IniStorage($sortStorage);

            case StorageFormat::PHP:
                return new PhpStorage($sortStorage);

            case StorageFormat::SHOPWARE6:
                return new Shopware6Storage();

            default:
                throw new \Exception('No storage found for format: ' . $format);
        }
    }

}
