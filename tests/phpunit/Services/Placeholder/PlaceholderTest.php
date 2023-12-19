<?php

namespace phpunit\Services\Placeholder;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Services\Placeholder\Placeholder;

class PlaceholderTest extends TestCase
{

    /**
     * @return void
     */
    public function testId(): void
    {
        $ph = new Placeholder('test');

        $this->assertEquals(md5('test'), $ph->getId());
    }

    /**
     * @return void
     */
    public function testValue(): void
    {
        $ph = new Placeholder('{firstname}');

        $this->assertEquals('{firstname}', $ph->getValue());
    }

}
