<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Bundles\Exchange;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Exchange\ImportEntry;
use PHPUnuhi\Bundles\Exchange\ImportResult;

class ImportResultTest extends TestCase
{
    public function testGetEntries(): void
    {
        $result = new ImportResult(
            [
                new ImportEntry('', '', '', ''),
                new ImportEntry('', '', '', ''),
            ]
        );

        $this->assertCount(2, $result->getEntries());
    }
}
