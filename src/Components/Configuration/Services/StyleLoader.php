<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Models\Configuration\CaseStyle;
use SimpleXMLElement;

class StyleLoader
{

    /**
     * @param SimpleXMLElement $stylesNode
     * @return CaseStyle[]
     */
    public function loadStyles(SimpleXMLElement $stylesNode): array
    {
        $styles = [];

        if ($stylesNode->style === null) {
            return [];
        }

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

        return $styles;
    }
}
