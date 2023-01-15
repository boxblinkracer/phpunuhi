<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\Type;
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
     * @param string $field
     * @param string $value
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateTranslation(string $entity, string $entityId, string $languageId, string $field, string $value): void
    {
        $sql = '
           UPDATE ' . $entity . '_translation  
            SET ' . $field . ' = :value
            WHERE ' . $entity . '_id = :entityID AND language_id = :languageId';

        $this->connection->executeQuery($sql,
            [
                'value' => $value,
                'entityID' => $this->stringtoBinary($entityId),
                'languageId' => $this->stringtoBinary($languageId),
            ]
        );
    }
}
