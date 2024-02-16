<?php

namespace PHPUnuhi\Traits;

use PHPUnuhi\Models\Configuration\Attribute;
use SimpleXMLElement;

trait XmlTrait
{

    /**
     * @param string $name
     * @param SimpleXMLElement $node
     * @return bool
     */
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

    /**
     * @param string $name
     * @param SimpleXMLElement $node
     * @return Attribute
     */
    protected function getAttribute(string $name, SimpleXMLElement $node): Attribute
    {
        $nodeAttributes = $node->attributes();

        if ($nodeAttributes !== null) {
            foreach ($nodeAttributes as $attrName => $value) {
                if ($attrName === $name) {
                    return new Attribute($attrName, $value);
                }
            }
        }

        return new Attribute($name, '');
    }

    /**
     * @param SimpleXMLElement $node
     * @return array<Attribute>
     */
    protected function getAttributes(SimpleXMLElement $node): array
    {
        $setAttributes = [];
        $nodeAttributes = $node->attributes();
        if ($nodeAttributes !== null) {
            foreach ($nodeAttributes as $attrName => $value) {
                $setAttributes[] = new Attribute($attrName, $value);
            }
        }

        return $setAttributes;
    }
}
