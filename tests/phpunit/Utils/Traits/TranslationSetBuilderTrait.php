<?php

declare(strict_types=1);

namespace PHPUnuhi\Tests\Utils\Traits;

use PHPUnuhi\Models\Configuration\CaseStyleSetting;
use PHPUnuhi\Models\Configuration\Filter;
use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Translation\Locale;
use PHPUnuhi\Models\Translation\TranslationSet;

trait TranslationSetBuilderTrait
{
    /**
     * @param Locale[] $locales
     * @param Rule[] $rules
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
            new CaseStyleSetting([], []),
            $rules
        );
    }
}
