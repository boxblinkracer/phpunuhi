<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Repository;

use PDO;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\Sw6Locale;
use PHPUnuhi\Traits\BinaryTrait;

class LanguageRepository
{
    use BinaryTrait;

    private PDO $pdo;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Sw6Locale[]
     */
    public function getLanguages(): array
    {
        $stmt = $this->pdo->prepare('SELECT l.id as langId, l.name as langName, lc.code as locCode FROM language l INNER JOIN locale lc ON lc.id = l.locale_id');
        $stmt->execute();

        $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = [];

        if (!is_array($dbRows)) {
            return $list;
        }

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
