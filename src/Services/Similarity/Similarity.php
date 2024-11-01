<?php

namespace PHPUnuhi\Services\Similarity;

class Similarity
{

    /**
     * @param string[] $keys
     * @param float $threshold
     * @return array<mixed>
     */
    public function findSimilarString(array $keys, float $threshold = 70.0): array
    {
        $similarPairs = [];

        foreach ($keys as $i => $key1) {
            $counter = count($keys);
            for ($j = $i + 1; $j < $counter; $j++) {
                $key2 = $keys[$j];
                $similarity = 0;
                similar_text($key1, $key2, $similarity);

                if ($similarity >= $threshold) {
                    $similarPairs[] = [
                        'key1' => $key1,
                        'key2' => $key2,
                        'similarity' => $similarity
                    ];
                }
            }
        }

        return $similarPairs;
    }
}
