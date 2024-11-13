<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

interface Rules
{
    public const NESTING_DEPTH = 'nestingDepth';


    public const KEY_LENGTH = 'keyLength';


    public const DISALLOWED_TEXT = 'disallowedTexts';


    public const DUPLICATE_CONTENT = 'duplicateContent';


    public const EMPTY_CONTENT = 'emptyContent';
}
