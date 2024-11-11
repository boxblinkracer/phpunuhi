<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6;

use Exception;
use PHPUnuhi\Bundles\Storage\Shopware6\Config\AppManifestXml;
use PHPUnuhi\Bundles\Storage\Shopware6\Config\FlowActionsXml;
use PHPUnuhi\Bundles\Storage\Shopware6\Config\PluginConfigXml;
use PHPUnuhi\Bundles\Storage\Shopware6\Config\ShopwareXmlInterface;
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

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $file;


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
        if ($this->type === 'config') {
            return 'xml';
        }
        return "-";
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
        $this->type = $set->getAttributeValue('type');


        if ($this->type === 'config') {
            $this->file = $set->getAttributeValue('file');
        } else {
            # type entity
            $host = (string)getenv('DB_HOST');
            $port = (string)getenv('DB_PORT');
            $user = (string)getenv('DB_USER');
            $pwd = (string)getenv('DB_PASSWD');
            $dbName = (string)getenv('DB_DBNAME');

            $factory = new ConnectionFactory();

            $connStr = $factory->buildMySQLConnectionString($host, $port, $dbName);

            $pdo = $factory->fromConnectionString($connStr, $user, $pwd);

            $this->loader = new TranslationLoader($pdo);
            $this->saver = new TranslationSaver($pdo);
        }
    }

    /**
     * @param TranslationSet $set
     * @throws ConfigurationException
     * @return void
     */
    public function loadTranslationSet(TranslationSet $set): void
    {
        if ($this->type === 'config') {
            foreach ($set->getLocales() as $locale) {

                # reload xml for every locale is currently required
                $this->validateConfigType($set->getLocales());

                $xmlAdapter = $this->getXmlAdapter($set->getLocales());

                $translations = $xmlAdapter->readTranslations($locale->getName());

                foreach ($translations as $translationKey => $value) {
                    $locale->addTranslation($translationKey, $value, '');
                }
            }
        } else {
            # type entity
            $this->loader->loadTranslations($set);
        }
    }

    /**
     * @param TranslationSet $set
     * @throws ConfigurationException
     * @return StorageSaveResult
     */
    public function saveTranslationSet(TranslationSet $set): StorageSaveResult
    {
        if ($this->type === 'config') {
            foreach ($set->getLocales() as $locale) {

                # reload xml for every locale is currently required
                $this->validateConfigType($set->getLocales());

                $xmlAdapter = $this->getXmlAdapter($set->getLocales());

                $xmlAdapter->writeTranslations($locale->getName(), $locale->getTranslations());
            }
        } else {
            return $this->saver->saveTranslations($set);
        }

        return new StorageSaveResult(0, 0);
    }

    /**
     * @param Locale $locale
     * @param string $filename
     * @throws Exception
     * @return StorageSaveResult
     */
    public function saveTranslationLocale(Locale $locale, string $filename): StorageSaveResult
    {
        if ($this->type === 'config') {
            $this->validateConfigType([$locale]);

            $xmlAdapter = $this->getXmlAdapter([$locale]);

            $xmlAdapter->writeTranslations($locale->getName(), $locale->getTranslations());

            return new StorageSaveResult(0, 0);
        }
        throw new Exception('Not supported at the moment');
    }

    /**
     * @param Locale[] $locales
     * @throws Exception
     * @return void
     */
    private function validateConfigType(array $locales): void
    {
        if (empty($this->file)) {
            throw new Exception('No "file" attribute configured. Please assign a the path to the configuration file in the storage format node');
        }

        # check if any of the locales are marked as base locale
        $baseLocaleFound = false;
        foreach ($locales as $locale) {
            if ($locale->isBase()) {
                $baseLocaleFound = true;
                break;
            }
        }

        if (!$baseLocaleFound) {
            throw new Exception('No base locale found. Please mark one of the locales as base locale');
        }
    }

    /**
     * @param Locale[] $locales
     * @throws Exception
     * @return ShopwareXmlInterface
     */
    private function getXmlAdapter(array $locales): ShopwareXmlInterface
    {
        $baseLocale = null;

        foreach ($locales as $locale) {
            if ($locale->isBase()) {
                $baseLocale = $locale;
                break;
            }
        }

        if (!$baseLocale instanceof Locale) {
            throw new Exception('No base locale found. Please mark one of the locales as base locale');
        }

        $xmlString = (string)file_get_contents($this->file);

        $xml = simplexml_load_string($xmlString);

        if ($xml === false) {
            throw new Exception('Error parsing XML');
        }

        if ($xml->getName() === 'config') {
            return new PluginConfigXml($this->file, $xmlString, $baseLocale->getName());
        }

        if ($xml->getName() === 'manifest') {
            return new AppManifestXml($this->file, $xmlString, $baseLocale->getName());
        }

        if ($xml->getName() === 'flow-extensions' || $xml->getName() === 'flow-actions') {
            return new FlowActionsXml($this->file, $xmlString, $baseLocale->getName());
        }

        throw new Exception('No Shopware XML Adapter found for configuration with tag: ' . $xml->getName());
    }
}
