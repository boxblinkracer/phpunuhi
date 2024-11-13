<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use PDO;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\UpdateField;
use PHPUnuhi\Traits\BinaryTrait;

class EntityTranslationRepository
{
    use BinaryTrait;

    private PDO $pdo;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array<mixed>
     */
    public function getTranslations(string $entity): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $entity . '_translation');

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!is_array($rows)) {
            return [];
        }

        return $rows;
    }

    /**
     * @param UpdateField[] $fieldValues
     */
    public function writeTranslation(string $entity, string $entityId, string $languageId, array $fieldValues): void
    {
        $existingRow = $this->getTranslationRow($entity, $entityId, $languageId);
        if ($existingRow === null || $existingRow === []) {
            $this->insertTranslationRow($entity, $entityId, $languageId, $fieldValues);
            return;
        }

        $this->updateTranslationRow($entity, $entityId, $languageId, $fieldValues);
    }

    /**
     * @return null|array<mixed>
     */
    public function getTranslationRow(string $entity, string $entityId, string $languageId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $entity . '_translation WHERE ' . $entity . '_id = :id AND language_id = :langId');

        $stmt->execute([
            ':id' => $this->stringToBinary($entityId),
            ':langId' => $this->stringToBinary($languageId),
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!is_array($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @param UpdateField[] $fieldValues
     */
    public function updateTranslationRow(string $entity, string $entityId, string $languageId, array $fieldValues): void
    {
        $tableName = $entity . '_translation';

        $columnValueMap = $this->prepareDatabaseDataValues($tableName, $fieldValues);

        $params = [
            ':id' => $this->stringToBinary($entityId),
            ':langId' => $this->stringToBinary($languageId),
        ];

        $sqlFieldParts = [];

        foreach ($columnValueMap as $column => $value) {
            $valueKey = ':value_' . $column;
            $sqlFieldParts[] = sprintf('%s = %s', $column, $valueKey);
            $params[$valueKey] = $value;
        }

        $sqlFieldParts[] = 'updated_at = now()';

        $setSql = implode(', ', $sqlFieldParts);
        $stmt = $this->pdo->prepare(sprintf('UPDATE %s SET %s WHERE %s_id = :id AND language_id = :langId', $tableName, $setSql, $entity));

        $stmt->execute($params);
    }

    /**
     * @param UpdateField[] $fieldValues
     */
    public function insertTranslationRow(string $entity, string $entityId, string $languageId, array $fieldValues): void
    {
        $tableName = $entity . '_translation';

        $columnValueMap = $this->prepareDatabaseDataValues($tableName, $fieldValues);
        $columnValueMap['language_id'] = $this->stringToBinary($languageId);
        $columnValueMap[$entity . '_id'] = $this->stringToBinary($entityId);

        $paramColValueMap = [];
        foreach ($columnValueMap as $column => $value) {
            $valueKey = ':value_' . $column;

            $params[$valueKey] = $value;
            $paramColValueMap[$column] = $valueKey;
        }

        $paramColValueMap['created_at'] = 'now()';

        $stmt = $this->pdo->prepare(sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(', ', array_keys($paramColValueMap)),
            implode(', ', array_values($paramColValueMap))
        ));

        $stmt->execute($params);
    }

    /**
     * @param UpdateField[] $fieldValues
     * @return array<string, null|string>
     */
    private function prepareDatabaseDataValues(string $tableName, array $fieldValues): array
    {
        $jsonFields = $this->getJsonFields($tableName);

        $columnValueMap = [];
        foreach ($fieldValues as $fieldValue) {
            $value = $fieldValue->getValue();

            # make sure empty JSON fields are NULL
            if ($value === '' && in_array($fieldValue->getField(), $jsonFields)) {
                $value = null;
            }

            $columnValueMap[$fieldValue->getField()] = $value;
        }

        return $columnValueMap;
    }

    /**
     * @return array<mixed>
     */
    private function getJsonFields(string $table): array
    {
        $stm = $this->pdo->prepare("DESCRIBE " . $table);
        $stm->execute();

        $columns = $stm->fetchAll(PDO::FETCH_ASSOC);

        $jsonFields = [];

        if (!is_array($columns)) {
            return $jsonFields;
        }

        foreach ($columns as $column) {
            if ($column['Type'] === 'json') {
                $jsonFields[] = $column['Field'];
            }
        }

        return $jsonFields;
    }
}
