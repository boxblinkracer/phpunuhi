<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Config;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMXPath;
use Exception;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\XmlHandler;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Traits\StringTrait;

class FlowActionsXml implements ShopwareXmlInterface
{
    use StringTrait;

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

    /**
     * @param string $locale
     * @return array|mixed[]
     */
    public function readTranslations(string $locale): array
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->xmlString);

        $xpath = new DOMXPath($dom);

        $results = [];


        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('//flow-actions/flow-action');

        foreach ($nodes as $action) {
            $meta = $this->getNode('meta', $action, $xpath);

            if (!$meta instanceof DOMElement) {
                throw new Exception('Meta node not found for flow action');
            }

            /** @var DOMElement $el */
            $el = $meta->getElementsByTagName('name')->item(0);

            $flowName = (string)$el->nodeValue;

            $keyPrefix = "flow-action." . $flowName;

            $metaLabel = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $meta);

            if ($metaLabel !== null) {
                $results[$keyPrefix . '.label'] = $metaLabel;
            }

            $metaDescription = $this->xmlHandler->findLocaleValue('description', $locale, $xpath, $meta);

            if ($metaDescription !== null) {
                $results[$keyPrefix . '.description'] = $metaDescription;
            }

            $configs = $this->getFlowActionConfigs($flowName, $xpath);

            foreach ($configs as $config) {

                /** @var DOMElement $el */
                $el = $config->getElementsByTagName('name')->item(0);

                $configName = (string)$el->nodeValue;

                $label = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $config);
                $placeholder = $this->xmlHandler->findLocaleValue('place-holder', $locale, $xpath, $config);
                $helpText = $this->xmlHandler->findLocaleValue('helpText', $locale, $xpath, $config);

                if ($label !== null) {
                    $results[$keyPrefix . '.config.' . $configName . '.label'] = $label;
                }

                if ($placeholder !== null) {
                    $results[$keyPrefix . '.config.' . $configName . '.place-holder'] = $placeholder;
                }

                if ($helpText !== null) {
                    $results[$keyPrefix . '.config.' . $configName . '.helpText'] = $helpText;
                }

                $options = $this->getFlowActionConfigOptions($configName, $flowName, $xpath);

                foreach ($options as $option) {
                    $optionValue = $option->getAttribute('value');

                    $optionLabel = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $option);

                    if ($optionLabel !== null) {
                        $results[$keyPrefix . '.config.' . $configName . '.option.' . $optionValue . '.label'] = $optionLabel;
                    }
                }
            }
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


        foreach ($translations as $translation) {
            $key = $translation->getKey();

            if ($this->stringDoesStartsWith($key, 'flow-action.')) {
                $actionName = explode('.', $key)[1];

                $action = $this->getFlowAction($actionName, $xpath);

                if ($action instanceof DOMElement) {
                    $meta = $this->getNode('meta', $action, $xpath);

                    if (!$meta instanceof DOMElement) {
                        throw new Exception('Meta node not found for flow action ' . $actionName);
                    }

                    $this->xmlHandler->updateNode('label', $locale, $translation, $meta, $dom, $xpath);
                    $this->xmlHandler->updateNode('description', $locale, $translation, $meta, $dom, $xpath);
                }

                if ($this->stringDoesContain($key, '.config.') && !$this->stringDoesContain($key, '.option.')) {
                    $configName = explode('.', $key)[3];

                    $config = $this->getFlowActionConfig($configName, $actionName, $xpath);

                    if ($config instanceof DOMElement) {
                        $this->xmlHandler->updateNode('label', $locale, $translation, $config, $dom, $xpath);
                        $this->xmlHandler->updateNode('place-holder', $locale, $translation, $config, $dom, $xpath);
                        $this->xmlHandler->updateNode('helpText', $locale, $translation, $config, $dom, $xpath);
                    }
                }

                if ($this->stringDoesContain($key, '.config.') && $this->stringDoesContain($key, '.option.')) {
                    $configName = explode('.', $key)[3];
                    $optionValue = explode('.', $key)[5];

                    $option = $this->getFlowActionConfigOption($optionValue, $configName, $actionName, $xpath);

                    if ($option instanceof DOMElement) {
                        $this->xmlHandler->updateNode('label', $locale, $translation, $option, $dom, $xpath);
                    }
                }
            }
        }
        // Save XML without changing original indents
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = true;

        $xml = $dom->saveXML();

        file_put_contents($this->filename, $xml);
    }

    private function getNode(string $node, DOMNode $contextNode, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//' . $node, $contextNode);

        foreach ($nodes as $foundNode) {
            return $foundNode;
        }

        return null;
    }

    /**
     * @param DOMXPath $xpath
     * @return DOMElement[]
     */
    private function getFlowActions(DOMXPath $xpath): array
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('//flow-actions/flow-action');

        return $nodes;
    }


    private function getFlowAction(string $actionName, DOMXPath $xpath): ?DOMElement
    {
        foreach ($this->getFlowActions($xpath) as $node) {

            /** @var DOMElement $el */
            $el = $node->getElementsByTagName('name')->item(0);

            $identifier = $el->nodeValue;

            if ($identifier === $actionName) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @param string $actionName
     * @param DOMXPath $xpath
     * @return DOMElement[]
     */
    public function getFlowActionConfigs(string $actionName, DOMXPath $xpath): array
    {
        $action = $this->getFlowAction($actionName, $xpath);

        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('//config/*', $action);
        return $nodes;
    }

    public function getFlowActionConfig(string $configName, string $actionName, DOMXPath $xpath): ?DOMElement
    {
        $configs = $this->getFlowActionConfigs($actionName, $xpath);

        foreach ($configs as $config) {

            /** @var DOMElement $el */
            $el = $config->getElementsByTagName('name')->item(0);

            $identifier = $el->nodeValue;

            if ($identifier === $configName) {
                return $config;
            }
        }

        return null;
    }

    /**
     * @param string $configName
     * @param string $actionName
     * @param DOMXPath $xpath
     * @return DOMElement[]
     */
    public function getFlowActionConfigOptions(string $configName, string $actionName, DOMXPath $xpath): array
    {
        $config = $this->getFlowActionConfig($configName, $actionName, $xpath);

        /** @var \DOMElement[] $nodes */
        $nodes = $xpath->query('//options/*', $config);

        return $nodes;
    }

    public function getFlowActionConfigOption(string $optionValue, string $configName, string $actionName, DOMXPath $xpath): ?DOMElement
    {
        $options = $this->getFlowActionConfigOptions($configName, $actionName, $xpath);

        foreach ($options as $option) {
            $identifier = $option->getAttribute('value');

            if ($identifier === $optionValue) {
                return $option;
            }
        }

        return null;
    }
}
