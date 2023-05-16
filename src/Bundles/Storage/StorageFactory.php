<?php

namespace PHPUnuhi\Bundles\Storage;

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
     * @param TranslationSet $set
     * @return StorageInterface
     * @throws ConfigurationException
     */
    public static function getStorage(TranslationSet $set): StorageInterface
    {
        $format = $set->getFormat();

        switch (strtolower($format)) {
            case 'json':
                $indent = $set->getAttributeValue('jsonIndent');
                $indent = ($indent === '') ? '2' : $indent;
                $sort = (bool)$set->getAttributeValue('sort');
                return new JsonStorage((int)$indent, $sort);

            case 'ini':
                $sort = (bool)$set->getAttributeValue('sort');
                return new IniStorage($sort);

            case 'php':
                $sort = (bool)$set->getAttributeValue('sort');
                return new PhpStorage($sort);

            case 'po':
                return new PoStorage();

            case 'yaml':
                $indent = $set->getAttributeValue('yamlIndent');
                $indent = ($indent === '') ? '2' : $indent;
                $sort = (bool)$set->getAttributeValue('sort');
                return new YamlStorage((int) $indent, $sort);

            case 'shopware6':
                return new Shopware6Storage();

            default:
                throw new ConfigurationException('No storage found for format: ' . $format);
        }
    }

}
