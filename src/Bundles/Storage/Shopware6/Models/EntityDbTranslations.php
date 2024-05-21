<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Models;

use PHPUnuhi\Traits\BinaryTrait;

class EntityDbTranslations
{
    use BinaryTrait;

    private const LANGUAGE_ID_KEY = 'language_id';

    private array $dbTranslations = [];
    private ?array $columnsWithTranslationsInDefaultLanguage = null;
    private string $entityIdKey;

    public function __construct(string $entity, array $dbTranslations, ?string $defaultLanguageId)
    {
        $this->entityIdKey = $entity . '_id';
        foreach ($dbTranslations as $dbTranslation) {
            $dbTranslation[$this->entityIdKey] = $this->binaryToString($dbTranslation[$this->entityIdKey]);
            $dbTranslation[self::LANGUAGE_ID_KEY] = $this->binaryToString($dbTranslation[self::LANGUAGE_ID_KEY]);

            $this->dbTranslations[$dbTranslation[self::LANGUAGE_ID_KEY]][] = $dbTranslation;

            if ($dbTranslation[self::LANGUAGE_ID_KEY] === $defaultLanguageId) {
                $this->columnsWithTranslationsInDefaultLanguage[$dbTranslation[$this->entityIdKey]] = array_flip(array_keys(array_filter($dbTranslation)));
            }
        }
    }

    public function getLanguageTranslations(string $languageId): array
    {
        if ($this->columnsWithTranslationsInDefaultLanguage) {
            return array_map(function ($a) {
                return array_intersect_key($a, $this->columnsWithTranslationsInDefaultLanguage[$a[$this->entityIdKey]]);
            }, $this->dbTranslations[$languageId]);
        }

        return $this->dbTranslations;
    }
}
