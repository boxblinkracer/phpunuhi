<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6;

use Doctrine\DBAL\Connection;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\TranslationLoader;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\TranslationSaver;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Connection\ConnectionFactory;

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
        $this->connection = (new ConnectionFactory())->fromEnv();

        $this->loader = new TranslationLoader($this->connection);
        $this->saver = new TranslationSaver($this->connection);
    }


    /**
     * @param TranslationSet $set
     * @return void
     * @throws \Doctrine\DBAL\Exception
     * @throws \PHPUnuhi\Exceptions\ConfigurationException
     */
    public function loadTranslations(TranslationSet $set): void
    {
        $this->loader->loadTranslations($set);
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     * @throws \Doctrine\DBAL\Exception
     * @throws \PHPUnuhi\Exceptions\ConfigurationException
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        return $this->saver->saveTranslations($set);
    }

}
