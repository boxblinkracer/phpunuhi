<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use PDO;
use PHPUnuhi\Traits\BinaryTrait;

class EntityRepository
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
     * @param string $id
     * @return array<mixed>
     */
    public function getEntity(string $entity, string $id): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM :tableName WHERE id = :id');

        $stmt->execute([
            'tableName' => $entity,
            'id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
