<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Storage\Shopware6\Service;

use Exception;
use PHPUnuhi\Bundles\Storage\Shopware6\Models\SnippetSet;

trait SnippetSetFinderTrait
{
    /**
     * @param SnippetSet[] $snippetSets
     */
    protected function findSnippetSet(array $snippetSets, string $name): SnippetSet
    {
        $filteredSnippetSets = array_filter(
            $snippetSets,
            function (SnippetSet $set) use ($name): bool {
                return $set->getName() === $name;
            }
        );

        if (count($filteredSnippetSets) !== 1) {
            $filteredSnippetSets = array_filter(
                $snippetSets,
                function (SnippetSet $set) use ($name): bool {
                    return $set->getIso() === $name;
                }
            );
        }

        $count = count($filteredSnippetSets);
        if ($count === 0) {
            throw new Exception('No Snippet Set found in Shopware for locale / name: ' . $name);
        }

        if ($count > 1) {
            throw new Exception('Found multiple Snippet Sets found in Shopware for locale / name: ' . $name);
        }

        return end($filteredSnippetSets);
    }
}
