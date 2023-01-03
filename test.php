<?php

echo PHP_EOL;

$snippets = [
    'storefront' => [
        'src/snippet' => [
            __DIR__ . '/../../src/Resources/snippet/de_DE/mollie-payments.de-DE.json',
            __DIR__ . '/../../src/Resources/snippet/en_GB/mollie-payments.en-GB.json',
            __DIR__ . '/../../src/Resources/snippet/nl_NL/mollie-payments.nl-NL.json',
        ],
    ],
    'admin' => [
        'src/snippet' => [
            __DIR__ . '/../../src/Resources/app/administration/src/snippet/de-DE.json',
            __DIR__ . '/../../src/Resources/app/administration/src/snippet/en-GB.json',
            __DIR__ . '/../../src/Resources/app/administration/src/snippet/nl-NL.json',
        ],
    ],
];

foreach ($snippets['admin'] as $scopeName => $files) {

    # all files of our scope belong together
    # meaning, they need to be "identical"
    $scopeSnippetCount = null;

    $foundSnippets = [];

    foreach ($files as $file) {
        $snippetJson = file_get_contents($file);
        $snippetArray = json_decode($snippetJson, true);

        $snippetArrayFlat = array_flat($snippetArray);

        $allKeys = array_keys($snippetArrayFlat);
        $allValues = array_values($snippetArrayFlat);

        if ($scopeSnippetCount === null) {
            # its our first
            $scopeSnippetCount = count($allKeys);
        }

        foreach ($allKeys as $key) {
            $value = $snippetArrayFlat[$key];
            if (empty($value)) {
                echo '** ERROR: Found empty snippet in file  \"' . $file . '\" for key: ' . $key . PHP_EOL;
                echo PHP_EOL;
                exit(1);
            }
        }

        foreach ($allKeys as $key) {
            $foundSnippets[$file][] = $key;
        }
    }


    # NOW COMPARE THAT THEY HAVE THE SAME STRUCTURE
    # ACROSS ALL FILES

    $previousFile = '';
    $previousKeys = null;
    foreach ($foundSnippets as $file => $snippetKeys) {

        if ($previousKeys !== null) {

            if (!arrayEqual($previousKeys, $snippetKeys)) {

                echo "Found difference in snippets in these files: " . PHP_EOL;
                echo "  - A: " . $previousFile . PHP_EOL;
                echo "  - B: " . $file . PHP_EOL;

                $filtered = array_diff($previousKeys, $snippetKeys);
                foreach ($filtered as $key) {
                    echo '           [x]: ' . $key . PHP_EOL;
                }

                echo PHP_EOL;
                echo PHP_EOL;
                echo '** ERROR: Found missing snippets across files!' . PHP_EOL;
                echo PHP_EOL;
                exit(1);
            }
        }

        $previousFile = $file;
        $previousKeys = $snippetKeys;
    }
}


echo 'Snippets are valid!' . PHP_EOL;
echo PHP_EOL;
exit(0);


# ---------------------------------------------------------------
/**
 * @param $array
 * @param $prefix
 * @return array
 */
function array_flat($array, $prefix = '')
{
    $result = array();

    foreach ($array as $key => $value) {
        $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

        if (is_array($value)) {
            $result = array_merge($result, array_flat($value, $new_key));
        } else {
            $result[$new_key] = $value;
        }
    }

    return $result;
}

function arrayEqual($a, $b)
{
    return (
        is_array($a)
        && is_array($b)
        && count($a) == count($b)
        && array_diff($a, $b) === array_diff($b, $a)
    );
}