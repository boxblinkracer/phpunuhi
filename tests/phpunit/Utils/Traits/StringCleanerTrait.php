<?php

namespace PHPUnuhi\Tests\Utils\Traits;

trait StringCleanerTrait
{

    /**
     * @param string $text
     * @return string
     */
    protected function buildComparableString(string $text): string
    {
        $text = str_replace("\n", '', $text);
        $text = str_replace("\t", '', $text);

        return str_replace(' ', '', $text);
    }
}
