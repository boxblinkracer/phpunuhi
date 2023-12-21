<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use DateTime;
use Exception;
use PDO;
use PHPUnuhi\Bundles\Storage\Shopware6\Exception\SnippetNotFoundException;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Snippet;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\SnippetSet;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\Uuid;
use PHPUnuhi\Traits\BinaryTrait;

class SnippetRepository
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
     * @throws Exception
     * @return SnippetSet[]
     */
    public function getSnippetSets(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM snippet_set');

        $stmt->execute();

        $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($dbRows !== (array)$dbRows) {
            throw new Exception('not found!');
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
     * @throws Exception
     * @return Snippet[]
     */
    public function getSnippets(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM snippet');

        $stmt->execute();

        $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($dbRows !== (array)$dbRows) {
            throw new Exception('not found!');
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
     * @throws SnippetNotFoundException
     * @return Snippet
     */
    public function getSnippet(string $key, string $snippetSetId): Snippet
    {
        $stmt = $this->pdo->prepare('SELECT * FROM snippet WHERE translation_key = :key AND snippet_set_id = :setId');

        $stmt->execute([
            'key' => $key,
            'setId' => $this->stringToBinary($snippetSetId)
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== (array)$row) {
            throw new SnippetNotFoundException('Snippet with key ' . $key . ' not found in set: ' . $snippetSetId);
        }

        return $this->toEntity($row);
    }

    /**
     * @param string $key
     * @throws SnippetNotFoundException
     * @return Snippet
     */
    public function getSnippetByKey(string $key): Snippet
    {
        $stmt = $this->pdo->prepare('SELECT * FROM snippet WHERE translation_key = :key');

        $stmt->execute([
            'key' => $key,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

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
     */
    public function updateSnippet(string $key, string $snippetSetId, string $value): void
    {
        $stmt = $this->pdo->prepare('UPDATE snippet SET value = :value WHERE translation_key = :key AND snippet_set_id = :setId');

        $stmt->execute([
            'value' => $value,
            'key' => $key,
            'setId' => $this->stringToBinary($snippetSetId),
        ]);
    }

    /**
     * @param string $key
     * @param string $snippetSetId
     * @param string $value
     * @param string $author
     * @param string $customFields
     * @throws Exception
     * @return void
     */
    public function insertSnippet(string $key, string $snippetSetId, string $value, string $author, string $customFields): void
    {
        if ($customFields === '') {
            $customFields = null;
        }

        $now = new DateTime('now');

        $stmt = $this->pdo->prepare("
            INSERT INTO snippet (id, translation_key, `value`, author, snippet_set_id, custom_fields, created_at)
            VALUES (:id, :translation_key, :value, :author, :snippet_set_id, :custom_fields, :created_at)
        ");

        $params = [
            ':id' => $this->stringToBinary(Uuid::randomHex()),
            ':translation_key' => $key,
            ':value' => $value,
            ':author' => $author,
            ':snippet_set_id' => $this->stringToBinary($snippetSetId),
            ':custom_fields' => $customFields,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ];

        $stmt->execute($params);
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
