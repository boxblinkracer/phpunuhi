<?php

namespace PHPUnuhi\Bundles\Storage\Shopware6\Config;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMXPath;
use Exception;
use PHPUnuhi\Bundles\Storage\Shopware6\Service\XmlHandler;
use PHPUnuhi\Models\Translation\Translation;
use PHPUnuhi\Traits\StringTrait;

class AppManifestXml implements ShopwareXmlInterface
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

        $meta = $this->getMeta($xpath);

        if (!$meta instanceof DOMElement) {
            throw new Exception('Could not find meta node in app manifest');
        }

        $label = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $meta);
        $description = $this->xmlHandler->findLocaleValue('description', $locale, $xpath, $meta);
        $privacyPolicy = $this->xmlHandler->findLocaleValue('privacyPolicyExtensions', $locale, $xpath, $meta);

        if ($label !== null) {
            $results["meta.label"] = $label;
        }

        if ($description !== null) {
            $results["meta.description"] = $description;
        }

        if ($privacyPolicy !== null) {
            $results["meta.privacyPolicyExtensions"] = $privacyPolicy;
        }


        $admin = $this->getAdmin($xpath);

        if ($admin instanceof DOMElement) {

            /** @var DOMElement[] $nodes */
            $nodes = $xpath->query('./action-button', $admin);

            foreach ($nodes as $actionButton) {
                $btnName = $actionButton->getAttribute('action');

                $keyPrefix = "admin.action-button." . $btnName;

                $label = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $actionButton);

                if ($label !== null) {
                    $results["$keyPrefix.label"] = $label;
                }
            }
        }

        $payments = $this->getPayments($xpath);

        if ($payments instanceof DOMElement) {

            /** @var DOMElement[] $nodes1 */
            $nodes1 = $xpath->query('./payment-method', $payments);

            foreach ($nodes1 as $paymentMethod) {

                /** @var DOMElement $el */
                $el = $paymentMethod->getElementsByTagName('identifier')->item(0);

                $paymentID = (string)$el->nodeValue;

                $label = $this->xmlHandler->findLocaleValue('name', $locale, $xpath, $paymentMethod);
                $description = $this->xmlHandler->findLocaleValue('description', $locale, $xpath, $paymentMethod);

                if ($label !== null) {
                    $results["payments." . $paymentID . ".name"] = $label;
                }

                if ($description !== null) {
                    $results["payments." . $paymentID . ".description"] = $description;
                }
            }
        }


        $customFields = $this->getCustomFields($xpath);

        if ($customFields instanceof DOMElement) {

            /** @var DOMElement[] $nodes2 */
            $nodes2 = $xpath->query('./custom-field-set', $customFields);

            foreach ($nodes2 as $customField) {

                /** @var DOMElement $el */
                $el = $customField->getElementsByTagName('name')->item(0);

                $customFieldID = (string)$el->nodeValue;

                $label = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $customField);

                if ($label !== null) {
                    $results["custom-fields." . $customFieldID . ".label"] = $label;
                }

                $fields = $this->getCustomFieldSetFields($customField, $xpath);

                foreach ($fields as $field) {
                    $fieldName = $field->getAttribute('name');

                    $fieldLabel = $this->xmlHandler->findLocaleValue('label', $locale, $xpath, $field);
                    $helpText = $this->xmlHandler->findLocaleValue('help-text', $locale, $xpath, $field);

                    if ($fieldLabel !== null) {
                        $results["custom-fields." . $customFieldID . ".fields." . $fieldName . ".label"] = $fieldLabel;
                    }

                    if ($helpText !== null) {
                        $results["custom-fields." . $customFieldID . ".fields." . $fieldName . ".help-text"] = $helpText;
                    }
                }
            }
        }

        $ruleConditions = $this->getRuleConditions($xpath);

        if ($ruleConditions instanceof DOMElement) {

            /** @var DOMElement[] $nodes3 */
            $nodes3 = $xpath->query('./rule-condition', $ruleConditions);

            foreach ($nodes3 as $ruleCondition) {

                /** @var DOMElement $el */
                $el = $ruleCondition->getElementsByTagName('identifier')->item(0);

                $ruleConditionID = (string)$el->nodeValue;

                $ruleKey = "rule-conditions." . $ruleConditionID;

                $name = $this->xmlHandler->findLocaleValue('name', $locale, $xpath, $ruleCondition);

                if ($name !== null) {
                    $results[$ruleKey . ".name"] = $name;
                }

                /** @var DOMElement[] $constraints */
                $constraints = $xpath->query('./constraints/*', $ruleCondition);

                foreach ($constraints as $constraint) {
                    $constraintId = $constraint->getAttribute('name');

                    $constraintKey = $ruleKey . ".constraint." . $constraintId;

                    $placeholder = $this->xmlHandler->findLocaleValue('placeholder', $locale, $xpath, $constraint);

                    if ($placeholder !== null) {
                        $results[$constraintKey . ".placeholder"] = $placeholder;
                    }

                    /** @var DOMElement[] $options */
                    $options = $xpath->query('./options/*', $constraint);

                    foreach ($options as $option) {
                        $optionId = $option->getAttribute('value');

                        $optionName = $this->xmlHandler->findLocaleValue('name', $locale, $xpath, $option);

                        if ($optionName !== null) {
                            $results[$constraintKey . ".options." . $optionId . '.name'] = $optionName;
                        }
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


        $meta = $this->getMeta($xpath);

        if (!$meta instanceof DOMElement) {
            throw new Exception('Could not find meta node in app manifest');
        }

        foreach ($translations as $translation) {
            $key = $translation->getKey();

            if ($key === 'meta.label') {
                $this->xmlHandler->updateNode('label', $locale, $translation, $meta, $dom, $xpath);
            }

            if ($key === 'meta.description') {
                $this->xmlHandler->updateNode('description', $locale, $translation, $meta, $dom, $xpath);
            }

            if ($key === 'meta.privacyPolicyExtensions') {
                $this->xmlHandler->updateNode('privacyPolicyExtensions', $locale, $translation, $meta, $dom, $xpath);
            }

            if ($this->stringDoesStartsWith($key, 'admin.action-button.')) {
                $admin = $this->getAdmin($xpath);

                if (!$admin instanceof DOMElement) {
                    throw new Exception('Could not find admin node in app manifest');
                }

                # now get the 3rd part of the action button which is the name
                $btnAction = explode('.', $key)[2];

                $actionButton = $this->getActionButton($btnAction, $admin, $xpath);

                if ($actionButton instanceof DOMElement) {
                    $this->xmlHandler->updateNode('label', $locale, $translation, $actionButton, $dom, $xpath);
                }
            }

            if ($this->stringDoesStartsWith($key, 'payments.')) {
                $payments = $this->getPayments($xpath);

                if (!$payments instanceof DOMElement) {
                    throw new Exception('Could not find payments node in app manifest');
                }

                # now get the 3rd part of the action button which is the name
                $paymentID = explode('.', $key)[1];

                $paymentMethod = $this->getPaymentMethod($paymentID, $payments, $xpath);

                if ($paymentMethod instanceof DOMElement) {
                    $this->xmlHandler->updateNode('name', $locale, $translation, $paymentMethod, $dom, $xpath);
                    $this->xmlHandler->updateNode('description', $locale, $translation, $paymentMethod, $dom, $xpath);
                }
            }

            if ($this->stringDoesStartsWith($key, 'custom-fields.') && !$this->stringDoesContain($key, '.fields.')) {
                $customFields = $this->getCustomFields($xpath);

                if (!$customFields instanceof DOMElement) {
                    throw new Exception('Could not find custom fields node in app manifest');
                }

                # now get the 3rd part of the action button which is the name
                $customFieldID = explode('.', $key)[1];

                $customField = $this->getCustomFieldSet($customFieldID, $customFields, $xpath);

                if ($customField instanceof DOMElement) {
                    $this->xmlHandler->updateNode('label', $locale, $translation, $customField, $dom, $xpath);
                }
            }

            if ($this->stringDoesStartsWith($key, 'custom-fields.') && $this->stringDoesContain($key, '.fields.')) {
                $customFields = $this->getCustomFields($xpath);

                if (!$customFields instanceof DOMElement) {
                    throw new Exception('Could not find custom fields node in app manifest');
                }

                $customFieldID = explode('.', $key)[1];
                $customField = $this->getCustomFieldSet($customFieldID, $customFields, $xpath);

                if ($customField instanceof DOMElement) {
                    $fieldName = explode('.', $key)[3];
                    $field = $this->getCustomFieldSetFields($customField, $xpath);

                    foreach ($field as $f) {
                        $fieldKey = $f->getAttribute('name');

                        if ($fieldKey === $fieldName) {
                            $this->xmlHandler->updateNode('label', $locale, $translation, $f, $dom, $xpath);
                            $this->xmlHandler->updateNode('help-text', $locale, $translation, $f, $dom, $xpath);
                        }
                    }
                }
            }

            if ($this->stringDoesStartsWith($key, 'rule-conditions.')) {
                $ruleConditions = $this->getRuleConditions($xpath);

                if (!$ruleConditions instanceof DOMElement) {
                    throw new Exception('Could not find rule conditions node in app manifest');
                }

                $ruleId = explode('.', $key)[1];
                $ruleCondition = $this->getRuleCondition($ruleId, $ruleConditions, $xpath);

                if ($ruleCondition instanceof DOMElement) {
                    $this->xmlHandler->updateNode('name', $locale, $translation, $ruleCondition, $dom, $xpath);

                    if ($this->stringDoesContain($key, '.constraint.') && !$this->stringDoesContain($key, '.options.')) {
                        $constraintName = explode('.', $key)[3];
                        $constraint = $this->getRuleConstraint($constraintName, $ruleCondition, $xpath);

                        if ($constraint instanceof DOMElement) {
                            $this->xmlHandler->updateNode('placeholder', $locale, $translation, $constraint, $dom, $xpath);
                        }
                    }

                    if ($this->stringDoesContain($key, '.constraint.') && $this->stringDoesContain($key, '.options.')) {
                        $constraintName = explode('.', $key)[3];
                        $optionName = explode('.', $key)[5];

                        $constraint = $this->getRuleConstraint($constraintName, $ruleCondition, $xpath);

                        if ($constraint instanceof DOMElement) {
                            $option = $this->getRuleConstraintOption($optionName, $constraint, $xpath);

                            if ($option instanceof DOMElement) {
                                $this->xmlHandler->updateNode('name', $locale, $translation, $option, $dom, $xpath);
                            }
                        }
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

    private function getMeta(DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//meta');

        foreach ($nodes as $node) {
            return $node;
        }

        return null;
    }

    private function getAdmin(DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//admin');

        foreach ($nodes as $node) {
            return $node;
        }

        return null;
    }

    private function getPayments(DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//payments');

        foreach ($nodes as $node) {
            return $node;
        }

        return null;
    }

    private function getCustomFields(DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//custom-fields');

        foreach ($nodes as $node) {
            return $node;
        }

        return null;
    }

    private function getRuleConditions(DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('.//rule-conditions');

        foreach ($nodes as $node) {
            return $node;
        }

        return null;
    }

    private function getActionButton(string $actionName, DOMElement $adminContext, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./action-button', $adminContext);

        foreach ($nodes as $node) {
            $action = $node->getAttribute('action');

            if ($actionName === $action) {
                return $node;
            }
        }

        return null;
    }

    private function getPaymentMethod(string $paymentID, DOMElement $paymentContext, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./payment-method', $paymentContext);

        foreach ($nodes as $node) {

            /** @var DOMElement $el */
            $el = $node->getElementsByTagName('identifier')->item(0);

            $identifier = $el->nodeValue;

            if ($identifier === $paymentID) {
                return $node;
            }
        }

        return null;
    }

    private function getCustomFieldSet(string $name, DOMElement $paymentContext, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./custom-field-set', $paymentContext);

        foreach ($nodes as $node) {

            /** @var DOMElement $el */
            $el = $node->getElementsByTagName('name')->item(0);

            $identifier = $el->nodeValue;

            if ($identifier === $name) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @param DOMElement $customFieldSet
     * @param DOMXPath $xpath
     * @return DOMElement[]
     */
    private function getCustomFieldSetFields(DOMElement $customFieldSet, DOMXPath $xpath): array
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./fields/*', $customFieldSet);

        return $nodes;
    }

    private function getRuleCondition(string $id, DOMElement $ruleContext, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./rule-condition', $ruleContext);

        foreach ($nodes as $node) {

            /** @var DOMElement $el */
            $el = $node->getElementsByTagName('identifier')->item(0);

            $identifier = $el->nodeValue;

            if ($identifier === $id) {
                return $node;
            }
        }

        return null;
    }

    private function getRuleConstraint(string $id, DOMElement $conditionContext, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./constraints/*', $conditionContext);

        foreach ($nodes as $node) {
            $identifier = $node->getAttribute('name');

            if ($identifier === $id) {
                return $node;
            }
        }

        return null;
    }

    private function getRuleConstraintOption(string $value, DOMElement $constraintContext, DOMXPath $xpath): ?DOMElement
    {
        /** @var DOMElement[] $nodes */
        $nodes = $xpath->query('./options/*', $constraintContext);

        foreach ($nodes as $node) {
            $identifier = $node->getAttribute('value');

            if ($identifier === $value) {
                return $node;
            }
        }

        return null;
    }
}
