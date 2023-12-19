<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6;

use Exception;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\TranslationLoader;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\TranslationSaver;
use PHPUnuhi\Bundles\Storage\StorageHierarchy;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Services\Connection\ConnectionFactory;

class Shopware6Storage implements StorageInterface
{

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
     * @return string
     */
    public function getStorageName(): string
    {
        return 'shopware6';
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return '-';
    }

    /**
     * @return bool
     */
    public function supportsFilters(): bool
    {
        return true;
    }

    /**
     * @return StorageHierarchy
     */
    public function getHierarchy(): StorageHierarchy
    {
        return new StorageHierarchy(
            false,
            ''
        );
    }

    /**
     * @param TranslationSet $set
     * @return void
     */
    public function configureStorage(TranslationSet $set): void
    {
        $pdo = (new ConnectionFactory())->pdoFromEnv();

        $this->loader = new TranslationLoader($pdo);
        $this->saver = new TranslationSaver($pdo);
    }

    /**
     * @param TranslationSet $set
     * @return void
     * @throws ConfigurationException
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        $this->loader->loadTranslations($set);
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     * @throws ConfigurationException
     */
    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        return $this->saver->saveTranslations($set);
    }

    /**
     * @param Locale $locale
     * @param string $filename
     * @return StorageSaveResult
     * @throws Exception
     */
    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        throw new Exception('Not supported at the moment');
    }

}
