<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Sw6Locale;
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
     * @return Sw6Locale[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLanguages(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('l.id as langId', 'l.name as langName', 'lc.code as locCode')
            ->from('language', 'l')
            ->join('l', 'locale', 'lc', 'lc.id = l.locale_id');

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

            $list[] = new Sw6Locale(
                $this->binaryToString((string)$row['langId']),
                (string)$row['langName'],
                (string)$row['locCode']
            );
        }

        return $list;
    }

}
