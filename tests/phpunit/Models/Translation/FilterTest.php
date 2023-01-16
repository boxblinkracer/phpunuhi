<?php

namespace phpunit\Models\Translation;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Translation\Filter;

class FilterTest extends TestCase
{


    /**
     * @return void
     */
    public function testExcludeWithWildcard()
    {
        $filter = new Filter();
        $filter->addExcludeKey('meta_*');

        $isAllowed = $filter->isKeyAllowed('meta_custom');

        $this->assertEquals(false, $isAllowed);
    }

}