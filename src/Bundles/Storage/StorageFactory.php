<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Bundles\Storage\JSON\JsonLoader;
use PHPUnuhi\Bundles\Storage\JSON\JsonSaver;
use PHPUnuhi\Bundles\Storage\JSON\JsonStorage;


class StorageFactory
{

    /**
     * @param string $format
     * @param int $jsonIntent
     * @param bool $jsonSort
     * @return StorageInterface
     * @throws \Exception
     */
    public static function getStorage(string $format, int $jsonIntent, bool $jsonSort): StorageInterface
    {
        switch (strtolower($format)) {
            case StorageFormat::JSON:
                return new JsonStorage($jsonIntent, $jsonSort);

            default:
                throw new \Exception('No storage found for format: ' . $format);
        }
    }

}
