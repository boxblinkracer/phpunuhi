<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Models\Configuration\Protection;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class ProtectionLoader
{
    use XmlTrait;

    /**
     * @param SimpleXMLElement $filterNode
     * @return Protection
     */
    public function loadProtection(SimpleXMLElement $filterNode): Protection
    {
        $protection = new Protection();

        $nodeMarkers = $filterNode->marker;
        $nodeTerms = $filterNode->term;

        if ($nodeMarkers !== null) {
            foreach ($nodeMarkers as $marker) {
                $markerStart = $this->getAttribute('start', $marker);
                $markerEnd = $this->getAttribute('end', $marker);

                $protection->addMarker($markerStart->getValue(), $markerEnd->getValue());
            }
        }

        if ($nodeTerms !== null) {
            foreach ($nodeTerms as $term) {
                $termValue = (string)$term;

                $protection->addTerm($termValue);
            }
        }

        return $protection;
    }
}
