<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreKey;

class CaseStyleSetting
{
    /** @var CaseStyle[] */
    private array $caseStyles;

    /** @var CaseStyleIgnoreKey[] */
    private array $ignoreKeys;


    /**
     * @param CaseStyle[] $caseStyles
     * @param CaseStyleIgnoreKey[] $ignoreKeys
     */
    public function __construct(array $caseStyles, array $ignoreKeys)
    {
        $this->caseStyles = $caseStyles;
        $this->ignoreKeys = $ignoreKeys;
    }

    /**
     * @return CaseStyle[]
     */
    public function getCaseStyles(): array
    {
        return $this->caseStyles;
    }

    /**
     * @return CaseStyleIgnoreKey[]
     */
    public function getIgnoreKeys(): array
    {
        return $this->ignoreKeys;
    }
}
