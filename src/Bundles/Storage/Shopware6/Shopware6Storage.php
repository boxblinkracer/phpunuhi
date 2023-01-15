<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\EntityTranslationRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\LanguageRepository;
use PHPUnuhi\Bundles\Storage\StorageInterface;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\BinaryTrait;
use PHPUnuhi\Traits\StringTrait;

class Shopware6Storage implements StorageInterface
{

    use BinaryTrait;
    use StringTrait;


    /**
     * @var Connection
     */
    private $connection;

    private const FIELD_BLACKLIST = [
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
                'dbname' => 'shopware',
                'user' => 'root',
                'password' => 'root',
                'host' => '127.0.0.1',
                'port' => 3306,
                'driver' => 'pdo_mysql',
            ],
            $config
        );
    }


    /**
     * @param TranslationSet $set
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadTranslations(TranslationSet $set): void
    {
        if (empty($set->getSw6Entity())) {
            throw new \Exception('No entity provided for Shopware6 Translation-Set: ' . $set->getName());
        }

        $repoLanguages = new LanguageRepository($this->connection);
        $repoTranslations = new EntityTranslationRepository($this->connection);

        $entity = $set->getSw6Entity();
        $entityIdKey = $entity . '_id';


        $allLanguages = $repoLanguages->getLanguages();


        foreach ($set->getLocales() as $locale) {

            $shopwareLanugageId = null;
            foreach ($allLanguages as $langEntity) {
                if ($langEntity->getLocaleName() === $locale->getName()) {
                    $shopwareLanugageId = $langEntity->getLanguageId();
                    break;
                }
            }

            if ($shopwareLanugageId === null) {
                throw new \Exception('no language found for locale: ' . $locale->getName());
            }

            $dbTranslations = $repoTranslations->getTranslations($entity);

            foreach ($dbTranslations as $dbRow) {

                $entityId = (string)$dbRow[$entityIdKey];

                $entityId = $this->binaryToString($entityId);
                $languageId = $this->binaryToString((string)$dbRow['language_id']);

                # if it's not the language we are looking for in our
                # locale, then just skip
                if ($languageId !== $shopwareLanugageId) {
                    continue;
                }

                # we need to create an identifier for our group data row
                # this is our product itself.
                $groupName = $entity . '_' . $entityId;

                foreach ($dbRow as $property => $value) {

                    if (!$set->getFilter()->isKeyAllowed($property)) {
                        continue;
                    }

                    # also exclude a few things hardcoded because they just make no sense
                    if (in_array($property, self::FIELD_BLACKLIST)) {
                        continue;
                    }

                    if ($this->stringEndsWith($property, '_id')) {
                        continue;
                    }


                    $rawValue = ($this->isBinary($value)) ? $this->binaryToString($value) : (string)$value;

                    # create a unique identifier for our translation
                    # it should contain our entity as well as the property
                    $transKey = 'sw6_' . $entity . '_' . $entityId . '.' . $property;

                    $locale->addTranslation($transKey, $rawValue, $groupName);
                }
            }
        }
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        return new StorageSaveResult(0, 0);
    }

}
