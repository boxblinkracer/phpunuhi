<?php

declare(strict_types=1);

namespace PHPUnuhi\Traits;

use PHPUnuhi\Models\Configuration\Attribute;
use SimpleXMLElement;

trait XmlTrait
{
    protected function hasAttribute(string $name, SimpleXMLElement $node): bool
    {
        $nodeAttributes = $node->attributes();

        if ($nodeAttributes !== null) {
            foreach ($nodeAttributes as $attrName => $value) {
                if ($attrName === $name) {
                    return true;
                }
            }
        }

        return false;
    }


    protected function getAttribute(string $name, SimpleXMLElement $node): Attribute
    {
        $nodeAttributes = $node->attributes();

        if ($nodeAttributes !== null) {
            foreach ($nodeAttributes as $attrName => $value) {
                if ($attrName === $name) {
                    return new Attribute($attrName, (string)$value);
                }
            }
        }

        return new Attribute($name, '');
    }

    /**
     * @return array<Attribute>
     */
    protected function getAttributes(SimpleXMLElement $node): array
    {
        $setAttributes = [];
        $nodeAttributes = $node->attributes();
        if ($nodeAttributes !== null) {
            foreach ($nodeAttributes as $attrName => $value) {
                $setAttributes[] = new Attribute($attrName, (string)$value);
            }
        }

        return $setAttributes;
    }
}
