<?php

namespace phpunit\Models\Configuration;

use PHPUnit\Framework\TestCase;
use PHPUnuhi\Models\Configuration\Configuration;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Translation\TranslationSet;

class ConfigurationTest extends TestCase
{

    /**
     * @return void
     */
    public function testTranslationSets()
    {
        $sets = [];
        $sets[] = new TranslationSet('products', 'ini', [], new Filter(), [], []);

        $config = new Configuration($sets);

        $this->assertCount(1, $config->getTranslationSets());
    }

}
