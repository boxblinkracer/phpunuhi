<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Types\Types;
use PHPUnuhi\Bundles\Storage\Shopware6\Exception\SnippetNotFoundException;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Snippet;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\SnippetSet;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\Uuid;
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

            $list[] = $this->toEntity($row);
        }

        return $list;
    }

    /**
     * @param string $key
     * @param string $snippetSetId
     * @return Snippet
     * @throws SnippetNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSnippet(string $key, string $snippetSetId): Snippet
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('s.*')
            ->from('snippet', 's')
            ->where($qb->expr()->eq('translation_key', ':key'))
            ->andWhere($qb->expr()->eq('snippet_set_id', ':setId'))
            ->setParameter('key', $key)
            ->setParameter('setId', $this->stringToBinary($snippetSetId), Types::BINARY);

        $result = $qb->execute();

        if (!$result instanceof Result) {
            throw new SnippetNotFoundException('Snippet with key ' . $key . ' not found in set: ' . $snippetSetId);
        }

        $row = $result->fetch();

        if ($row !== (array)$row) {
            throw new SnippetNotFoundException('Snippet with key ' . $key . ' not found in set: ' . $snippetSetId);
        }

        return $this->toEntity($row);
    }

    /**
     * @param string $key
     * @return Snippet
     * @throws SnippetNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSnippetByKey(string $key): Snippet
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('s.*')
            ->from('snippet', 's')
            ->where($qb->expr()->eq('translation_key', ':key'))
            ->setParameter('key', $key);

        $result = $qb->execute();

        if (!$result instanceof Result) {
            throw new SnippetNotFoundException('Snippet with key ' . $key);
        }

        $row = $result->fetch();

        if ($row !== (array)$row) {
            throw new SnippetNotFoundException('Snippet with key ' . $key);
        }

        return $this->toEntity($row);
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

        $value = utf8_decode($value);

        $qb->update('snippet')
            ->set('value', ':value')
            ->where($qb->expr()->eq('translation_key', ':key'))
            ->andWhere($qb->expr()->eq('snippet_set_id', ':setId'))
            ->setParameter('key', $key)
            ->setParameter('setId', $this->stringToBinary($snippetSetId), Types::BINARY)
            ->setParameter('value', $value);

        $qb->execute();
    }

    /**
     * @param string $key
     * @param string $snippetSetId
     * @param string $value
     * @param string $author
     * @param string $customFields
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function insertSnippet(string $key, string $snippetSetId, string $value, string $author, string $customFields): void
    {
        $qb = $this->connection->createQueryBuilder();

        if ($customFields === '') {
            $customFields = null;
        }

        $value = utf8_decode($value);

        $now = new \DateTime('now');

        $qb->insert('snippet')
            ->values([
                'id' => ':id',
                'translation_key' => ':key',
                'value' => ':value',
                'author' => ':author',
                'snippet_set_id' => ':snippetSetId',
                'custom_fields' => ':customFields',
                'created_at' => ':createdAt',
            ])
            ->setParameters([
                'id' => $this->stringToBinary(Uuid::randomHex()),
                'key' => $key,
                'value' => $value,
                'author' => $author,
                'snippetSetId' => $this->stringToBinary($snippetSetId),
                'customFields' => $customFields,
                'createdAt' => $now->format('Y-m-d H:i:s'),
            ]);

        $qb->execute();
    }

    /**
     * @param array<mixed> $row
     * @return Snippet
     */
    private function toEntity(array $row): Snippet
    {
        return new Snippet(
            $this->binaryToString((string)$row['id']),
            $this->binaryToString((string)$row['snippet_set_id']),
            (string)$row['translation_key'],
            (string)$row['value'],
            (string)$row['author'],
            (string)$row['custom_fields']
        );

    }


}