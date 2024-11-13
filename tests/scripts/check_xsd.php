<?php

if (isset($argc) && $argc > 1) {
    $version = $argv[1];

    // Extract the major and minor parts of the version (e.g., "1.22.1" -> "1.22")
    $minorVersion = preg_replace('/(\.\d+)?$/', '', $version);

    $filePath = __DIR__ . "/../../schema/{$minorVersion}.xsd";

    if (file_exists($filePath)) {
        echo "Success: Schema file found: {$filePath}\n";
        exit(0);
    } else {
        echo "Error: Schema file not found: {$filePath}\n";
        exit(1);
    }
} else {
    echo "Usage: php check_xsd.php <version>\n";
    exit(1);
}
