<?php

namespace PHPUnuhi\Configuration\Services;

use PHPUnuhi\Models\Configuration\Coverage;
use PHPUnuhi\Models\Configuration\Coverage\TranslationSetCoverage;
use PHPUnuhi\Traits\XmlTrait;
use SimpleXMLElement;

class CoverageLoader
{
    use XmlTrait;

    /**
     * @param SimpleXMLElement $coverageNode
     * @return Coverage
     */
    public function loadGlobalCoverage(SimpleXMLElement $coverageNode): Coverage
    {
        $coverage = new Coverage();

        $totalCoverage = $this->getAttribute('minCoverage', $coverageNode);

        if ($totalCoverage->getValue() != '') {
            $coverage->setMinCoverage((int)$totalCoverage->getValue());
        }

        if ($coverageNode->locale !== null) {
            foreach ($coverageNode->locale as $localeNode) {
                $nodeType = $localeNode->getName();

                if ($nodeType !== 'locale') {
                    continue;
                }

                $localeName = (string)$localeNode['name'];
                $localeCoverage = (int)$localeNode[0];

                $coverage->addLocaleCoverage($localeName, $localeCoverage);
            }
        }

        return $coverage;
    }

    /**
     * @param SimpleXMLElement $coverageNode
     * @return TranslationSetCoverage
     */
    public function loadTranslationCoverage(SimpleXMLElement $coverageNode): TranslationSetCoverage
    {
        $coverage = new TranslationSetCoverage();

        $totalCoverage = $this->getAttribute('minCoverage', $coverageNode);

        if ($totalCoverage->getValue() != '') {
            $coverage->setMinCoverage((int)$totalCoverage->getValue());
        }

        if ($coverageNode->locale !== null) {
            foreach ($coverageNode->locale as $localeNode) {
                $nodeType = $localeNode->getName();

                if ($nodeType !== 'locale') {
                    continue;
                }

                $localeName = (string)$localeNode['name'];
                $localeCoverage = (int)$localeNode[0];

                $coverage->addLocaleCoverage($localeName, $localeCoverage);
            }
        }

        return $coverage;
    }
}
