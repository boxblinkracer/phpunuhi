<?php

namespace PHPUnuhi\Bundles\Translation;

use PHPUnuhi\Models\Translation\Locale;

interface TranslationLoaderInterface
{

    /**
     * @param Locale $locale
     * @return void
     */
    function loadTranslations(Locale $locale): void;

}
