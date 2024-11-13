<?php

declare(strict_types=1);

namespace PHPUnuhi\Bundles\Exchange;

class ImportResult
{
    /**
     * @var ImportEntry[]
     */
    private array $entries;


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
