<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnuhi\Traits\BinaryTrait;

class LanguageRepository
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
     * @return array<mixed>
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLanguages(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('*')
            ->from('language', 'l');

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
     * @param string $name
     * @return string
     * @throws \Doctrine\DBAL\Exception
     */
    public function findIdByName(string $name): string
    {
        $languages = $this->getLanguages();

        foreach ($languages as $language) {

            if ($name === $language['name']) {
                return $this->binaryToString($language['id']);
            }
        }

        throw new \Exception('Language with name: ' . $name . ' not found in Shopware');
    }

}
