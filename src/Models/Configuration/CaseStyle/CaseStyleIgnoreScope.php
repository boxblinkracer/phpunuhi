<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration\CaseStyle;

interface CaseStyleIgnoreScope
{
    /**
     * This means that the provided key is ignored globally,
     * independent of it's nested level.
     */
    public const SCOPE_GLOBAL = 'global';

    /**
     * This means that the provided key is ignored only on the
     * level it was defined. A fully qualified key is required.
     */
    public const SCOPE_FIXED = 'fixed';
}
