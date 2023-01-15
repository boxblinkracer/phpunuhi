<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Service;

use Doctrine\DBAL\Connection;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Sw6Locale;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\EntityTranslationRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\LanguageRepository;
use PHPUnuhi\Bundles\Storage\StorageSaveResult;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\Locale;
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
        if (empty($set->getSw6Entity())) {
            throw new ConfigurationException('No entity configured for Shopware6 Translation-Set: ' . $set->getName());
        }

        $entity = $set->getSw6Entity();

        $allDbLanguages = $this->repoLanguages->getLanguages();

        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {

            $currentLanguageID = $this->getShopwareLanguageId($locale, $allDbLanguages);

            if (empty($currentLanguageID)) {
                throw new \Exception('no language found for locale: ' . $locale->getName());
            }

            $localeCount++;

            foreach ($locale->getTranslations() as $translation) {

                $entityId = str_replace($entity . '_', '', $translation->getGroup());

                try {
                    $this->repoTranslations->updateTranslation(
                        $entity,
                        $entityId,
                        $currentLanguageID,
                        $translation->getKey(),
                        $translation->getValue()
                    );

                    $translationCount++;
                } catch (\Exception $ex) {

                }
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
