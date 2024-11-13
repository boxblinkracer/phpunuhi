<?php

declare(strict_types=1);

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Models\Configuration\Filter;
use SimpleXMLElement;

class FilterLoader
{
    public function loadFilter(SimpleXMLElement $filterNode): Filter
    {
        $filter = new Filter();

        $nodeAllows = $filterNode->include;
        $nodeExcludes = $filterNode->exclude;

        $nodeAllowsKeys = ($nodeAllows !== null) ? $nodeAllows->key : null;
        $nodeExcludeKeys = ($nodeExcludes !== null) ? $nodeExcludes->key : null;

        if ($nodeAllowsKeys !== null) {
            foreach ($nodeAllowsKeys as $key) {
                $filter->addIncludeKey((string)$key);
            }
        }

        if ($nodeExcludeKeys !== null) {
            foreach ($nodeExcludeKeys as $key) {
                $filter->addExcludeKey((string)$key);
            }
        }

        return $filter;
    }
}
