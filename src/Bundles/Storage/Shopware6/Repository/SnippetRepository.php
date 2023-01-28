<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\Types;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Snippet;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\SnippetSet;
use PHPUnuhi\Traits\BinaryTrait;

class SnippetRepository
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
     * @return SnippetSet[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSnippetSets(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('s.*')
            ->from('snippet_set', 's');

        $result = $qb->execute();

        if (!$result instanceof Result) {
            return [];
        }

        $dbRows = $result->fetchAll();

        if ($dbRows !== (array)$dbRows) {
            throw new \Exception('not found!');
        }

        $list = [];

        foreach ($dbRows as $row) {

            $list[] = new SnippetSet(
                $this->binaryToString((string)$row['id']),
                (string)$row['name'],
                (string)$row['iso']
            );
        }

        return $list;
    }

    /**
     * @return Snippet[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSnippets(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('s.*')
            ->from('snippet', 's');

        $result = $qb->execute();

        if (!$result instanceof Result) {
            return [];
        }

        $dbRows = $result->fetchAll();

        if ($dbRows !== (array)$dbRows) {
            throw new \Exception('not found!');
        }

        $list = [];

        foreach ($dbRows as $row) {

            $list[] = new Snippet(
                $this->binaryToString((string)$row['id']),
                $this->binaryToString((string)$row['snippet_set_id']),
                (string)$row['translation_key'],
                (string)$row['value'],
                (string)$row['author']
            );
        }

        return $list;
    }

    /**
     * @param string $key
     * @param string $snippetSetId
     * @param string $value
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateSnippet(string $key, string $snippetSetId, string $value): void
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->update('snippet')
            ->set('value', ':value')
            ->where($qb->expr()->eq('translation_key', ':key'))
            ->andWhere($qb->expr()->eq('snippet_set_id', ':setId'))
            ->setParameter('key', $key)
            ->setParameter('setId', $this->stringToBinary($snippetSetId), Types::BINARY)
            ->setParameter('value', $value);

        $qb->executeQuery();
    }

}