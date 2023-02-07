<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\UpdateField;
use PHPUnuhi\Traits\BinaryTrait;

class EntityTranslationRepository
{

    use BinaryTrait;

    /**
     * @var Connection
     */
    private $connection;


    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $entity
     * @return array<mixed>
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTranslations(string $entity): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('*')
            ->from($entity . '_translation', 't');

        $result = $qb->execute();

        if (!$result instanceof Result) {
            return [];
        }

        $dbRows = $result->fetchAll();

        if ($dbRows !== (array)$dbRows) {
            throw new \Exception('not found!');
        }

        return $dbRows;
    }

    /**
     * @param string $entity
     * @param string $entityId
     * @param string $languageId
     * @return array<mixed>|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTranslationRow(string $entity, string $entityId, string $languageId): ?array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('*')
            ->from($entity . '_translation', 't')
            ->where($qb->expr()->eq($entity . '_id', ':id'))
            ->andWhere($qb->expr()->eq('language_id', ':langId'))
            ->setParameter('id', $this->stringToBinary($entityId))
            ->setParameter('langId', $this->stringToBinary($languageId));

        $result = $qb->execute();

        if (!$result instanceof Result) {
            return null;
        }

        $dbRow = $result->fetch();

        if ($dbRow !== (array)$dbRow) {
            return null;
        }

        return $dbRow;
    }

    /**
     * @param string $entity
     * @param string $entityId
     * @param string $languageId
     * @param UpdateField[] $fieldValues
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateTranslationRow(string $entity, string $entityId, string $languageId, array $fieldValues): void
    {
        $tableName = $entity . '_translation';

        $jsonFields = $this->getJsonFields($tableName);

        $qb = $this->connection->createQueryBuilder();
        $qb->update($tableName);

        # now iterate through our parameter fields
        # unfortunately, we have to assign NULL for every empty JSON field-value.
        # otherwise we get a JSON empty-document error
        foreach ($fieldValues as $data) {

            $valueKey = 'value_' . $data->getField();
            $value = $data->getValue();

            $value = utf8_decode($value);

            # make sure empty JSON fields are NULL
            if ($value === '' && in_array($data->getField(), $jsonFields)) {
                $value = NULL;
            }


            $qb->set($data->getField(), ':' . $valueKey);
            $qb->setParameter($valueKey, $value);
        }

        $qb->where($qb->expr()->eq($entity . '_id', ':id'))
            ->andWhere($qb->expr()->eq('language_id', ':langId'))
            ->setParameter('id', $this->stringToBinary($entityId), Types::BINARY)
            ->setParameter('langId', $this->stringToBinary($languageId), Types::BINARY);

        $qb->execute();
    }

    /**
     * @param string $table
     * @return array<mixed>
     */
    private function getJsonFields(string $table): array
    {
        $sm = $this->connection->getSchemaManager();
        $columns = $sm->listTableColumns($table);

        $jsonFields = [];

        foreach ($columns as $column) {
            if ($column->getType()->getName() === 'json') {
                $jsonFields[] = $column->getName();
            }
        }

        return $jsonFields;
    }

}
