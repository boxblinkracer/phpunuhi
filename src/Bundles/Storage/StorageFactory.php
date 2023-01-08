<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Bundles\Storage\INI\IniStorage;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;


class StorageFactory
{

    /**
     * @param string $format
     * @param int $jsonIntent
     * @param bool $sortStorage
     * @return StorageInterface
     * @throws \Exception
     */
    public static function getStorage(string $format, int $jsonIntent, bool $sortStorage): StorageInterface
    {
        switch (strtolower($format)) {
            case StorageFormat::JSON:
                return new JsonStorage($jsonIntent, $sortStorage);

            case StorageFormat::INI:
                return new IniStorage($sortStorage);

            default:
                throw new \Exception('No storage found for format: ' . $format);
        }
    }

}
