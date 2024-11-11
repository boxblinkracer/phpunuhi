<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Components\Validator\DuplicateContent\DuplicateContent;
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
            $value = (string)$nestingDepth;
            if ($value !== '') {
                $rules[] = new Rule(Rules::NESTING_DEPTH, $value);
            }
        }

        if ($keyLength !== null) {
            $value = (string)$keyLength;
            if ($value !== '') {
                $rules[] = new Rule(Rules::KEY_LENGTH, $value);
            }
        }

        if ($disallowedTexts !== null) {
            /** @var null|array<mixed> $textsArray */
            $textsArray = $disallowedTexts->text;
            if ($textsArray !== null && count($textsArray) > 0) {
                $rules[] = new Rule(Rules::DISALLOWED_TEXT, (array)$textsArray);
            }
        }

        if ($duplicateContent !== null && $duplicateContent->children() instanceof SimpleXMLElement) {
            $foundLocales = [];
            /** @var SimpleXMLElement $localeNode */
            foreach ($duplicateContent->children() as $localeNode) {
                $localeName = $this->getAttribute('name', $localeNode)->getValue();
                $localeValue = (strtolower((string)$localeNode[0]) === 'true');
                $foundLocales[] = new DuplicateContent($localeName, $localeValue);
            }
            $rules[] = new Rule(Rules::DUPLICATE_CONTENT, $foundLocales);
        }

        if ($emptyContent !== null && $emptyContent->children() instanceof SimpleXMLElement) {
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
