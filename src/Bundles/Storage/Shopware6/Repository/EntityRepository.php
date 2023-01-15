<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnuhi\Traits\BinaryTrait;

class EntityRepository
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
     * @param string $id
     * @return array<mixed>
     * @throws \Doctrine\DBAL\Exception
     */
    public function getEntity(string $entity, string $id): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('*')
            ->from($entity, 't')
            ->where('id = :id')
            ->setParameters(
                [
                    ':id' => $id,
                ]
            );

        $result = $qb->execute();

        if (!$result instanceof Result) {
            return [];
        }

        $dbRow = $result->fetch();

        return $dbRow;
    }

}
