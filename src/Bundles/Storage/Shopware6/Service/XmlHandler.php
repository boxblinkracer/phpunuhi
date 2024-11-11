<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Service;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMXPath;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Traits\StringTrait;

class XmlHandler
{
    use StringTrait;

    private const LANG_ATTRIBUTE = 'lang';

    /**
     * @var string
     */
    private $defaultLocale;


    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param string $tagName
     * @param string $locale
     * @param DOMXPath $xpath
     * @param DOMElement $contextNode
     * @return null|string
     */
    public function findLocaleValue(string $tagName, string $locale, DOMXPath $xpath, DOMElement $contextNode): ?string
    {
        $locale = strtolower($locale);

        /** @var DOMNode[] $nodes */
        $nodes = $xpath->query('./' . $tagName, $contextNode);

        foreach ($nodes as $node) {
            if ($node instanceof DOMElement) {
                # the default locale has no attribute
                if ($locale === strtolower($this->defaultLocale) && !$node->hasAttribute(self::LANG_ATTRIBUTE)) {
                    return trim((string)$node->nodeValue);
                }

                $langAttr = $node->getAttribute(self::LANG_ATTRIBUTE);

                if (strtolower($langAttr) === $locale) {
                    return trim((string)$node->nodeValue);
                }
            }
        }

        return null;
    }

    /**
     * @param string $tagName
     * @param string $locale
     * @param Translation $translation
     * @param DOMNode $contextNode
     * @param DOMDocument $document
     * @param DOMXPath $xpath
     * @throws DOMException
     * @return void
     */
    public function updateNode(string $tagName, string $locale, Translation $translation, DOMNode $contextNode, DOMDocument $document, DOMXPath $xpath): void
    {
        $document->encoding = 'UTF-8';

        $value = html_entity_decode($translation->getValue(), ENT_QUOTES | ENT_XML1, 'UTF-8');

        # we check  if we have something like card.0.title or card.0.input.xyz
        if ($this->stringDoesEndsWith($translation->getKey(), '.' . $tagName)) {
            $isDefaultLocale = strtolower($locale) === strtolower($this->defaultLocale);

            $foundNodeForLocale = null;
            $foundOtherExistingNode = null;
            $foundDefaultLocaleNode = null; # en-GB

            /** @var DOMNode[] $nodes1 */
            $nodes1 = $xpath->query('./' . $tagName . '[@' . self::LANG_ATTRIBUTE . '="' . $locale . '"]', $contextNode);

            foreach ($nodes1 as $tmpNode) {
                $foundNodeForLocale = $tmpNode;
                break;
            }

            /** @var DOMNode[] $nodes2 */
            $nodes2 = $xpath->query('./' . $tagName . '[not(@' . self::LANG_ATTRIBUTE . ')]', $contextNode);

            foreach ($nodes2 as $tmpNode) {
                $foundDefaultLocaleNode = $tmpNode;
                break;
            }

            /** @var DOMNode[] $nodes3 */
            $nodes3 = $xpath->query('./' . $tagName, $contextNode);

            foreach ($nodes3 as $tmpNode) {
                $foundOtherExistingNode = $tmpNode;
                break;
            }

            # if we have the default locale without the attribute, and we have found it
            # assign this as our found node
            if ($isDefaultLocale && $foundDefaultLocaleNode !== null) {
                $foundNodeForLocale = $foundDefaultLocaleNode;
            }

            if ($foundNodeForLocale === null) {
                # create new entry
                $newNode = $document->createElement($tagName, $value);

                # the default locale has no lang attribute
                if (!$isDefaultLocale) {
                    $newNode->setAttribute(self::LANG_ATTRIBUTE, $locale);
                }

                if ($foundOtherExistingNode !== null) {
                    $contextNode->insertBefore($newNode, $foundOtherExistingNode);
                } else {
                    $contextNode->appendChild($newNode);
                }
            } else {
                # already existing
                $foundNodeForLocale->nodeValue = $value;
            }
        }
    }
}
