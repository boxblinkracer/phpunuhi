<?php

namespace PHPUnuhi\Commands;

interface CommandNames
{
    public const STATUS = 'status';

    public const VALIDATE = 'validate';
    public const VALIDATE_ALL = 'validate:all';
    public const VALIDATE_COVERAGE = 'validate:coverage';
    public const VALIDATE_MESS = 'validate:mess';
    public const VALIDATE_SPELLING = 'validate:spelling';
    public const VALIDATE_STRUCTURE = 'validate:structure';

    public const LIST_TRANSLATIONS = 'list:translations';
}
