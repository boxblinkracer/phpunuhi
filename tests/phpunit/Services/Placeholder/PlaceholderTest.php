<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Services\Placeholder;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Placeholder\Placeholder;

class PlaceholderTest extends TestCase
{
    public function testId(): void
    {
        $ph = new Placeholder('test');

        $this->assertEquals(md5('test'), $ph->getId());
    }


    public function testValue(): void
    {
        $ph = new Placeholder('{firstname}');

        $this->assertEquals('{firstname}', $ph->getValue());
    }
}
