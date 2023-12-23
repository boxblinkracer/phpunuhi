<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use SimpleXMLElement;

class RulesLoader
{

    /**
     * @param SimpleXMLElement $rulesNode
     * @return Rule[]
     */
    public function loadRules(SimpleXMLElement $rulesNode): array
    {
        $rules = [];

        $nestingDepth = $rulesNode->nestingDepth;
        $keyLength = $rulesNode->keyLength;
        $disallowedTexts = $rulesNode->disallowedTexts;
        $duplicateContent = $rulesNode->duplicateContent;

        if ($nestingDepth !== null) {
            $rules[] = new Rule(Rules::NESTING_DEPTH, (string)$nestingDepth);
        }

        if ($keyLength !== null) {
            $rules[] = new Rule(Rules::KEY_LENGTH, (string)$keyLength);
        }

        if ($disallowedTexts !== null) {
            $textsArray = $disallowedTexts->text;
            $rules[] = new Rule(Rules::DISALLOWED_TEXT, (array)$textsArray);
        }

        if ($duplicateContent !== null) {
            $value = (strtolower($duplicateContent));
            if ($value !== '') {
                $isAllowed = $value !== 'false';
                $rules[] = new Rule(Rules::DUPLICATE_CONTENT, $isAllowed);
            }
        }

        return $rules;
    }
}
