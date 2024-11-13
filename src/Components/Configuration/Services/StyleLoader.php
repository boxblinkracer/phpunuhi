<?php

declare(strict_types=1);

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyle;
use PHPUnuhi\Models\Configuration\CaseStyle\CaseStyleIgnoreKey;
use PHPUnuhi\Traits\BoolTrait;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class StyleLoader
{
    use XmlTrait;
    use BoolTrait;

    /**
     * @return CaseStyle[]
     */
    public function loadStyles(SimpleXMLElement $stylesNode): array
    {
        $styles = [];

        if ($stylesNode->style !== null) {
            foreach ($stylesNode->style as $style) {
                $attributes = $style->attributes();

                $styleName = (string)$style;
                $styleLevel = ($attributes instanceof SimpleXMLElement) ? (string)$attributes->level : '';

                $caseStyle = new CaseStyle($styleName);

                if ($styleLevel !== '' && $styleLevel !== '0') {
                    $caseStyle->setLevel((int)$styleLevel);
                }

                $styles[] = $caseStyle;
            }
        }

        return $styles;
    }

    /**
     * @return CaseStyleIgnoreKey[]
     */
    public function loadIgnoredKeys(SimpleXMLElement $stylesNode): array
    {
        $ignoredKeys = [];

        if ($stylesNode->ignore !== null) {
            if ($stylesNode->ignore->key === null) {
                return $ignoredKeys;
            }

            /** @var SimpleXMLElement $ignoreEntry */
            foreach ($stylesNode->ignore->key as $ignoreEntry) {
                $key = (string)$ignoreEntry;

                $hasAttribute = $this->hasAttribute('fqp', $ignoreEntry);

                # DEFAULT value -> always be strict => TRUE
                $isFQP = true;

                if ($hasAttribute) {
                    $fqpStrValue = $this->getAttribute('fqp', $ignoreEntry)->getValue();
                    $isFQP = $this->getBool($fqpStrValue);
                }

                $ignoredKeys[] = new CaseStyleIgnoreKey($key, $isFQP);
            }
        }

        return $ignoredKeys;
    }
}
