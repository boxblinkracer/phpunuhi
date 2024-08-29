<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Service;

use Exception;
use PDO;
use PHPUnuhi\Bundles\Storage\Shopware6\Exception\SnippetNotFoundException;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Sw6Locale;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\UpdateField;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\EntityTranslationRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\LanguageRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\SnippetRepository;
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
    use SnippetSetFinderTrait;

    /**
     * @var LanguageRepository
     */
    private $repoLanguages;

    /**
     * @var EntityTranslationRepository
     */
    private $repoTranslations;

    /**
     * @var SnippetRepository
     */
    private $repoSnippets;


    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->repoLanguages = new LanguageRepository($pdo);
        $this->repoTranslations = new EntityTranslationRepository($pdo);
        $this->repoSnippets = new SnippetRepository($pdo);
    }

    /**
     * @param TranslationSet $set
     * @throws ConfigurationException
     * @return StorageSaveResult
     */
    public function saveTranslations(TranslationSet $set): StorageSaveResult
    {
        $entity = $set->getAttributeValue('entity');

        if ($entity === '' || $entity === '0') {
            throw new ConfigurationException('No entity configured for Shopware6 Translation-Set: ' . $set->getName());
        }

        if (strtolower($entity) === 'snippet') {
            return $this->saveSnippets($set);
        }

        return $this->saveEntities($entity, $set);
    }


    /**
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    private function saveSnippets(TranslationSet $set): StorageSaveResult
    {
        $localeCount = 0;
        $translationCount = 0;


        $allSnippetSets = $this->repoSnippets->getSnippetSets();

        foreach ($set->getLocales() as $locale) {
            $localeCount++;

            $foundSnippetSet = $this->findSnippetSet($allSnippetSets, $locale->getName());
            foreach ($locale->getTranslations() as $translation) {
                try {
                    $existingSnippet = $this->repoSnippets->getSnippet($translation->getKey(), $foundSnippetSet->getId());

                    $this->repoSnippets->updateSnippet(
                        $translation->getKey(),
                        $foundSnippetSet->getId(),
                        $translation->getValue()
                    );
                } catch (SnippetNotFoundException $ex) {
                    $existingSnippet = $this->repoSnippets->getSnippetByKey($translation->getKey());

                    $this->repoSnippets->insertSnippet(
                        $translation->getKey(),
                        $foundSnippetSet->getId(),
                        $translation->getValue(),
                        $existingSnippet->getAuthor(),
                        $existingSnippet->getCustomFields()
                    );
                }


                $translationCount++;
            }
        }

        return new StorageSaveResult(
            $localeCount,
            $translationCount
        );
    }

    /**
     * @param string $entity
     * @param TranslationSet $set
     * @return StorageSaveResult
     */
    private function saveEntities(string $entity, TranslationSet $set): StorageSaveResult
    {
        $allDbLanguages = $this->repoLanguages->getLanguages();

        $localeCount = 0;
        $translationCount = 0;

        foreach ($set->getLocales() as $locale) {
            $currentLanguageID = $this->getShopwareLanguageId($locale, $allDbLanguages);

            if ($currentLanguageID === '' || $currentLanguageID === '0') {
                throw new Exception('no language found for locale: ' . $locale->getName());
            }

            $localeCount++;

            $entityUpdateData = [];

            # improve MySQL update by adding all translations
            # of a single entity to just 1 SQL statement
            foreach ($locale->getTranslations() as $translation) {
                $entityUpdateData[$translation->getGroup()][] = $translation;
            }

            # now that we have grouped them,
            # build a single SQL update statement for every entity (group)
            foreach ($entityUpdateData as $group => $translations) {
                $fields = [];
                $entityId = '';

                /** @var Translation $translation */
                foreach ($translations as $translation) {
                    $fields[] = new UpdateField($translation->getKey(), $translation->getValue());
                    $entityId = str_replace($entity . '_', '', $translation->getGroup());
                }

                # check if even existing
                $existingRow = $this->repoTranslations->getTranslationRow($entity, $entityId, $currentLanguageID);

                if ($existingRow === null) {
                    echo "   [!] Translation not existing (Group: " . $group . "). PHPUnuhi cannot create new entries at the moment: " . $entity . '_' . $entityId . PHP_EOL;
                    continue;
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

        echo PHP_EOL;

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
