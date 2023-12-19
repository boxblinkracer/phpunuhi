<?php

namespace PHPUnuhi\Models\Configuration;

class Protection
{

    /**
     * @var Marker[]
     */
    private $markers = [];

    /**
     * @var string[]
     */
    private $terms = [];

    /**
     * @param string $start
     * @param string $end
     * @return void
     */
    public function addMarker(string $start, string $end): void
    {
        $this->markers[] = new Marker($start, $end);
    }

    /**
     * @param string $term
     * @return void
     */
    public function addTerm(string $term): void
    {
        if (in_array($term, $this->terms)) {
            return;
        }

        $this->terms[] = $term;
    }

    /**
     * @return array|Marker[]
     */
    public function getMarkers(): array
    {
        return $this->markers;
    }

    /**
     * @return array|string[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }

}
