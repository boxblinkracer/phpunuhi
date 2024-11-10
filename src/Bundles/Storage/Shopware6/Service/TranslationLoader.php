<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Service;

use Exception;
use PDO;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Sw6Locale;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\EntityTranslationRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\LanguageRepository;
use PHPUnuhi\Bundles\Storage\Shopware6\Repository\SnippetRepository;
use PHPUnuhi\Exceptions\ConfigurationException;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;
use PHPUnuhi\Traits\BinaryTrait;
use PHPUnuhi\Traits\StringTrait;

class TranslationLoader
{
    use BinaryTrait;
    use StringTrait;
    use SnippetSetFinderTrait;

    public const FIELD_BLACKLIST = [
        'created_at',
        'updated_at',
    ];


    /**
     * @var LanguageRepository
     */
    private $repoLanguages;

    /**
     * @var EntityTranslationRepository
     */
    private $repoEntityTranslations;

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
        $this->repoEntityTranslations = new EntityTranslationRepository($pdo);
        $this->repoSnippets = new SnippetRepository($pdo);
    }


    /**
     * @param TranslationSet $set
     * @throws ConfigurationException
     * @return void
     */
    public function loadTranslations(TranslationSet $set): void
    {
        $entity = $set->getAttributeValue('entity');

        if ($entity === '' || $entity === '0') {
            throw new ConfigurationException('No entity configured for Shopware6 Translation-Set: ' . $set->getName());
        }

        if (strtolower($entity) === 'snippet') {
            $this->loadSnippets($set);
        } else {
            $this->loadEntities($entity, $set);
        }
    }


    /**
     * @param TranslationSet $set
     * @return void
     */
    private function loadSnippets(TranslationSet $set): void
    {
        $allSnippets = $this->repoSnippets->getSnippets();
        $allSnippetSets = $this->repoSnippets->getSnippetSets();

        foreach ($set->getLocales() as $locale) {
            try {
                $snippetSet = $this->findSnippetSet($allSnippetSets, $locale->getName());
            } catch (Exception $e) {
                continue;
            }

            foreach ($allSnippets as $snippet) {
                if ($snippetSet->getId() !== $snippet->getSnippetSetId()) {
                    continue;
                }

                $locale->addTranslation(
                    $snippet->getKey(),
                    $snippet->getValue(),
                    ''
                );
            }
        }
    }

    /**
     * @param string $entity
     * @param TranslationSet $set
     * @return void
     */
    private function loadEntities(string $entity, TranslationSet $set): void
    {
        $entityIdKey = $entity . '_id';

        $allDbLanguages = $this->repoLanguages->getLanguages();
        $allDbTranslations = $this->repoEntityTranslations->getTranslations($entity);

        foreach ($set->getLocales() as $locale) {
            $currentLanguageID = $this->getShopwareLanguageId($locale, $allDbLanguages);

            if ($currentLanguageID === '' || $currentLanguageID === '0') {
                throw new Exception('no language found for locale: ' . $locale->getName());
            }

            foreach ($allDbTranslations as $dbRow) {

                # read our primary entity id and the
                # language id of the current translation row
                $entityId = $this->binaryToString((string)$dbRow[$entityIdKey]);
                $languageId = $this->binaryToString((string)$dbRow['language_id']);

                # if it's not the language we are looking for, just skip
                if ($languageId !== $currentLanguageID) {
                    continue;
                }

                # we need to create a group-identifier for our row. this is the current entity object
                # for the product entity, a group will be a single product (T-Shirt A)
                # in the end, multiple translations exist for that group (product T-Shirt A)
                $groupName = $entity . '_' . $entityId;

                # now iterate through all columns.
                # these columns are the properties and will be treated
                # as separate translation entries within our current group.
                foreach ($dbRow as $property => $value) {

                    # also exclude a few things hardcoded because they just make no sense
                    if (in_array($property, self::FIELD_BLACKLIST)) {
                        continue;
                    }

                    if ($this->stringDoesEndsWith($property, '_id')) {
                        continue;
                    }

                    # create a unique identifier for our translation
                    # it should contain our entity as well as the property
                    $translationKey = $property;

                    # the value might either be a string or binary
                    # make sure to always use raw string
                    $translationValue = ($this->isBinary((string)$value)) ? $this->binaryToString($value) : (string)$value;

                    $locale->addTranslation($translationKey, $translationValue, $groupName);
                }
            }
        }
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
