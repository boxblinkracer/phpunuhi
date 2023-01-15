<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
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

}
