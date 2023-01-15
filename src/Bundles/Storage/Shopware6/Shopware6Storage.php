<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\TranslationLoader;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\TranslationSaver;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\TranslationSet;

class Shopware6Storage implements StorageInterface
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var TranslationLoader
     */
    private $loader;

    /**
     * @var TranslationSaver
     */
    private $saver;


    public const FIELD_BLACKLIST = [
        'created_at',
        'updated_at',
    ];


    /**
     *
     */
    public function __construct()
    {
        $config = new Configuration();

        $this->connection = \Doctrine\DBAL\DriverManager::getConnection(
            [
                'host' => $_SERVER['DB_HOST'],
                'port' => $_SERVER['DB_PORT'],
                'user' => $_SERVER['DB_USER'],
                'password' => $_SERVER['DB_PASSWD'],
                'dbname' => $_SERVER['DB_DBNAME'],
                'driver' => 'pdo_mysql',
            ],
            $config
        );

        $this->loader = new TranslationLoader($this->connection);
        $this->saver = new TranslationSaver($this->connection);
    }


    /**
     * @param TranslationSet $set
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadTranslations(TranslationSet $set): void
    {
        $this->loader->loadTranslations($set);
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        return $this->saver->saveTranslations($set);
    }

}
