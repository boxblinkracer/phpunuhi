<?php

namespace PHPUnuhi\Services\Placeholder;

class PlaceholderExtractor
{

    /**
     * @param string $text
     * @param string $markerStart
     * @param string $markerEnd
     * @return Placeholder[]
     * @return Placeholder[]
     */
    public function extract(string $text, string $markerStart, string $markerEnd): array
    {
        $foundValues = [];

        $regex = '~\\' . $markerStart . "(.*?)" . '\\' . $markerEnd . '~';

        preg_match_all($regex, $text, $foundValues);

        $uniqueList = [];

        $resultsWithoutMarkers = $foundValues[1];

        foreach ($resultsWithoutMarkers as $result) {
            if (!in_array($result, $uniqueList)) {
                $uniqueList[] = $result;
            }
        }

        $placeholders = [];

        foreach ($uniqueList as $foundValue) {

            $ph = new Placeholder($markerStart . $foundValue . $markerEnd);

            $placeholders[] = $ph;
        }

        return $placeholders;
    }


}