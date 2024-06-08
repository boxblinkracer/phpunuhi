<?php

namespace PHPUnuhi\Tests\Bundles\Exchange;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Bundles\Exchange\ImportEntry;

class ImportEntryTest extends TestCase
{

    /**
     * @return void
     */
    public function testLocaleExchangeID(): void
    {
        $entry = new ImportEntry('ex-id', '', '', '');

        $this->assertEquals('ex-id', $entry->getLocaleExchangeID());
    }

    /**
     * @return void
     */
    public function testKey(): void
    {
        $entry = new ImportEntry('', 'key-123', '', '');

        $this->assertEquals('key-123', $entry->getKey());
    }

    /**
     * @return void
     */
    public function testValue(): void
    {
        $entry = new ImportEntry('', '', '', 'value-123');

        $this->assertEquals('value-123', $entry->getValue());
    }

    /**
     * @return void
     */
    public function testGroup(): void
    {
        $entry = new ImportEntry('', '', 'en', '');

        $this->assertEquals('en', $entry->getGroup());
    }
}
