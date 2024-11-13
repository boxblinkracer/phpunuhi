<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Traits;

trait StringCleanerTrait
{
    protected function buildComparableString(string $text): string
    {
        $text = str_replace("\n", '', $text);
        $text = str_replace("\t", '', $text);

        return str_replace(' ', '', $text);
    }
}
