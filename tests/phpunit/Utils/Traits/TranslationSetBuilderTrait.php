<?php

namespace phpunit\Utils\Traits;

use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Translation\TranslationSet;

trait TranslationSetBuilderTrait
{

    /**
     * @param array $locales
     * @param array $rules
     * @return TranslationSet
     */
    protected function buildTranslationSet(array $locales, array $rules): TranslationSet
    {
        return new TranslationSet(
            'Storefront',
            'json',
            new Protection(),
            $locales,
            new Filter(),
            [],
            [],
            $rules
        );
    }

}