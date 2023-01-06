<?php

namespace PHPUnuhi\Bundles\Exchange;

use PHPUnuhi\Models\Translation\TranslationSet;

interface ImportInterface
{

    /**
     * @param TranslationSet $set
     * @param string $filename
     * @return ImportResult
     */
    function import(TranslationSet $set, string $filename): ImportResult;

}
