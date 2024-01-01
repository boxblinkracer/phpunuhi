<?php

namespace phpunit\Bundles\Exchange;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Exchange\ImportEntry;
use PHPUnuhi\Bundles\Exchange\ImportResult;

class ImportResultTest extends TestCase
{

    /**
     * @return void
     */
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
