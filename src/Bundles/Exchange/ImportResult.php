<?php

namespace PHPUnuhi\Bundles\Exchange;

class ImportResult
{

    /**
     * @var ImportEntry[]
     */
    private $entries;


    /**
     * @param ImportEntry[] $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * @return ImportEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

}
