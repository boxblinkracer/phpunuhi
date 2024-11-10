<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Config;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNodeList;
use DOMXPath;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\XmlHandler;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Traits\StringTrait;

class PluginConfigXml implements ShopwareXmlInterface
{
    use StringTrait;

    const LENGTH_WITH_TITLE = 3;


    /**
     * @var XmlHandler
     */
    private $xmlHandler;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $xmlString;


    public function __construct(string $filename, string $xmlString, string $defaultLocale)
    {
        $this->xmlString = $xmlString;
        $this->filename = $filename;

        $this->xmlHandler = new XmlHandler($defaultLocale);
    }


    public function readTranslations(string $locale): array
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->xmlString);

        $xpath = new DOMXPath($dom);

        $results = [];
        $cardIndex = 0;

        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('//card');

        foreach ($nodes as $card) {
            $cardPrefix = "card.$cardIndex";

            $title = $this->xmlHandler->findLocaleValue('title', $locale, $xpath, $card);

            if ($title !== null) {
                $results["$cardPrefix.title"] = $title;
            }

            /** @var DOMElement[] $nodes2 */
            $nodes2 = $xpath->query('.//input-field', $card);

            foreach ($nodes2 as $input) {

                /** @var DOMNodeList<DOMElement> $inputNodes */
                $inputNodes = $xpath->query('./name', $input);

                $inputNode = $inputNodes->item(0);

                if ($inputNode === null) {
                    continue;
                }

                $inputName = $inputNode->nodeValue;
                $inputPrefix = "$cardPrefix.input.$inputName";

                $label = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $input);
                $helpText = $this->xmlHandler->findLocaleValue('helpText', $locale, $xpath, $input);

                if ($label !== null) {
                    $results["$inputPrefix.label"] = $label;
                }

                if ($helpText !== null) {
                    $results["$inputPrefix.helpText"] = $helpText;
                }
            }

            $cardIndex++;
        }

        return $results;
    }

    /**
     * @param string $locale
     * @param Translation[] $translations
     * @throws DOMException
     * @return void
     */
    public function writeTranslations(string $locale, array $translations): void
    {
        $dom = new DOMDocument();

        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        $dom->loadXML($this->xmlString);
        $xpath = new DOMXPath($dom);

        $cardIndex = 0;

        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('//card');

        foreach ($nodes as $card) {
            $cardPrefix = "card.$cardIndex";

            foreach ($translations as $translation) {
                $key = $translation->getKey();

                # check if we have the correct card, like card.0.xyz
                if (!$this->stringDoesStartsWith($key, $cardPrefix)) {
                    continue;
                }

                $keyParts = explode('.', $key);

                if (count($keyParts) === self::LENGTH_WITH_TITLE && $keyParts[2] === 'title') {
                    $this->xmlHandler->updateNode('title', $locale, $translation, $card, $dom, $xpath);
                }

                if ($this->stringDoesContain($key, '.input.')) {
                    $inputName = $keyParts[3];
                    $input = $this->getInput($xpath, $inputName, $card);

                    if ($input instanceof DOMElement) {
                        if ($this->stringDoesEndsWith($key, '.label')) {
                            $this->xmlHandler->updateNode('label', $locale, $translation, $input, $dom, $xpath);
                        }

                        if ($this->stringDoesEndsWith($key, '.helpText')) {
                            $this->xmlHandler->updateNode('helpText', $locale, $translation, $input, $dom, $xpath);
                        }
                    }
                }
            }

            $cardIndex++;
        }

        // Save XML without changing original indents
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = true;

        $xml = $dom->saveXML();

        file_put_contents($this->filename, $xml);
    }

    private function getInput(DOMXPath $xpath, string $name, DOMElement $card): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//input-field[name="' . $name . '"]', $card);

        foreach ($nodes as $existingInput) {
            return $existingInput;
        }
        return null;
    }
}
