<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use PDO;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\UpdateField;
use PHPUnuhi\Traits\BinaryTrait;

class EntityTranslationRepository
{
    use BinaryTrait;

    /**
     * @var \PDO
     */
    private $pdo;


    /**
     * @param \PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $entity
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
     * @param string $entity
     * @param string $entityId
     * @param string $languageId
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
     * @param string $entity
     * @param string $entityId
     * @param string $languageId
     * @param UpdateField[] $fieldValues
     * @return void
     */
    public function updateTranslationRow(string $entity, string $entityId, string $languageId, array $fieldValues): void
    {
        $tableName = $entity . '_translation';

        $jsonFields = $this->getJsonFields($tableName);

        $params = [
            ':id' => $this->stringToBinary($entityId),
            ':langId' => $this->stringToBinary($languageId),
        ];

        $sqlFieldParts = [];

        # now iterate through our parameter fields
        # unfortunately, we have to assign NULL for every empty JSON field-value.
        # otherwise we get a JSON empty-document error
        foreach ($fieldValues as $data) {
            $valueKey = 'value_' . $data->getField();
            $value = $data->getValue();

            $value =  mb_convert_encoding($value, "ISO-8859-1", "UTF-8");

            # make sure empty JSON fields are NULL
            if ($value === '' && in_array($data->getField(), $jsonFields)) {
                $value = null;
            }

            $sqlFieldParts[] = $data->getField() . '= :' . $valueKey;
            $params[':' . $valueKey] = $value;
        }

        $setSql = implode(', ', $sqlFieldParts);

        $stmt = $this->pdo->prepare('UPDATE ' . $entity . '_translation SET ' . $setSql . ' WHERE ' . $entity . '_id = :id AND language_id = :langId');

        $stmt->execute($params);
    }

    /**
     * @param string $table
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
