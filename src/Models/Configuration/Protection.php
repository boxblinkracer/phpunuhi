<?php

declare(strict_types=1);

namespace PHPUnuhi\Models\Configuration;

class Protection
{
    /**
     * @var Marker[]
     */
    private array $markers = [];

    /**
     * @var string[]
     */
    private array $terms = [];


    public function addMarker(string $start, string $end): void
    {
        $this->markers[] = new Marker($start, $end);
    }


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
