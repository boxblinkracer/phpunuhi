<?php

namespace PHPUnuhi\Bundles\Storage;

use PHPUnuhi\Bundles\Storage\JSON\JsonLoader;
use PHPUnuhi\Bundles\Storage\JSON\JsonSaver;


class StorageFactory
{


    /**
     * @param string $format
     * @return StorageLoaderInterface
     * @throws \Exception
     */
    public static function getLoaderFromFormat(string $format): StorageLoaderInterface
    {
        switch (strtolower($format)) {
            case StorageFormat::JSON:
                return new JsonLoader();

            default:
                throw new \Exception('No storage loader found for format: ' . $format);
        }
    }

    /**
     * @param string $format
     * @param int $jsonIntent
     * @param bool $jsonSort
     * @return StorageSaverInterface
     * @throws \Exception
     */
    public static function getSaverFromFormat(string $format, int $jsonIntent, bool $jsonSort): StorageSaverInterface
    {
        switch (strtolower($format)) {
            case StorageFormat::JSON:
                return new JsonSaver($jsonIntent, $jsonSort);

            default:
                throw new \Exception('No storage saver found for format: ' . $format);
        }
    }

}
