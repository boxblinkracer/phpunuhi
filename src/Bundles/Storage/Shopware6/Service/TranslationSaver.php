<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Service;

use Doctrine\DBAL\Connection;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Sw6Locale;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\UpdateField;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\EntityTranslationRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\LanguageRepository;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\BinaryTrait;
use PHPUnuhi\Traits\StringTrait;

class TranslationSaver
{

    use BinaryTrait;
    use StringTrait;

    /**
     * @var LanguageRepository
     */
    private $repoLanguages;

    /**
     * @var EntityTranslationRepository
     */
    private $repoTranslations;


    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->repoLanguages = new LanguageRepository($connection);
        $this->repoTranslations = new EntityTranslationRepository($connection);
    }

    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     * @throws ConfigurationException
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        if (empty($set->getEntity())) {
            throw new ConfigurationException('No entity configured for Shopware6 Translation-Set: ' . $set->getName());
        }

        $entity = $set->getEntity();

        $allDbLanguages = $this->repoLanguages->getLanguages();

        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {

            $currentLanguageID = $this->getShopwareLanguageId($locale, $allDbLanguages);

            if (empty($currentLanguageID)) {
                throw new \Exception('no language found for locale: ' . $locale->getName());
            }

            $localeCount++;

            $entityUpdateData = [];

            # improve MySQL update by adding all translations
            # of a single entity to just 1 SQL statement
            foreach ($locale->getTranslations() as $translation) {
                $entityUpdateData[$translation->getGroup()][] = $translation;
            }

            # now that we have grouped them,
            # build a single SQL udpate statement for every entity (group)
            foreach ($entityUpdateData as $group => $translations) {

                $fields = [];
                $entityId = '';

                /** @var Translation $translation */
                foreach ($translations as $translation) {
                    $fields[] = new UpdateField($translation->getKey(), $translation->getValue());
                    $entityId = str_replace($entity . '_', '', $translation->getGroup());
                }

                $this->repoTranslations->updateTranslationRow(
                    $entity,
                    $entityId,
                    $currentLanguageID,
                    $fields
                );

                $translationCount++;
            }
        }

        return new StorageSaveResult(
            $localeCount,
            $translationCount
        );
    }

    /**
     * @param Locale $locale
     * @param Sw6Locale[] $allLanguages
     * @return string
     */
    private function getShopwareLanguageId(Locale $locale, array $allLanguages): string
    {
        foreach ($allLanguages as $langEntity) {
            if ($langEntity->getLocaleName() === $locale->getName()) {
                return $langEntity->getLanguageId();
            }
        }

        return '';
    }

}