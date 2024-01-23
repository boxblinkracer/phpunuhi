<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Components\Validator\EmptyContent\AllowEmptyContent;
use PHPUnuhi\Models\Configuration\Rule;
use PHPUnuhi\Models\Configuration\Rules;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class RulesLoader
{
    use XmlTrait;

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
        $emptyContent = $rulesNode->emptyContent;

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

        if ($emptyContent !== null && $emptyContent->children() !== null) {
            $foundKeys = [];
            foreach ($rulesNode->emptyContent->children() as $keyNode) {
                $keyName = $this->getAttribute('name', $keyNode)->getValue();
                $foundLocales = [];
                /** @var SimpleXMLElement $localeNode */
                foreach ($keyNode->children() as $localeNode) {
                    $localeName = (string)$localeNode[0];
                    $foundLocales[] = $localeName;
                }
                $foundKeys[] = new AllowEmptyContent($keyName, $foundLocales);
            }
            $rules[] = new Rule(Rules::EMPTY_CONTENT, $foundKeys);
        }

        return $rules;
    }
}
