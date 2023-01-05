<?php

namespace PHPUnuhi\Bundles;

use PHPUnuhi\Models\Translation\Locale;

interface TranslationLoaderInterface
{

    /**
     * @param Locale $locale
     * @return void
     */
    function loadTranslations(Locale $locale): void;

}
