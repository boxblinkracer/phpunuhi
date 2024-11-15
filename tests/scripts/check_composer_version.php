<?php

if (isset($argc) && $argc > 1) {
    $inputVersion = $argv[1];
} else {
    echo "Usage: php check_composer_version.php <version>\n";
    exit(1);
}

// Read the composer.json file
$composerJsonPath = __DIR__ . '/../../composer.json';
if (!file_exists($composerJsonPath)) {
    echo "Error: composer.json not found.\n";
    exit(1);
}

$composerData = json_decode(file_get_contents($composerJsonPath), true);
if ($composerData === null || !isset($composerData['version'])) {
    echo "Error: Version not found in composer.json.\n";
    exit(1);
}

$currentVersion = $composerData['version'];

// Check if the current version is dev-main and ignore it
if ($inputVersion === 'dev-main') {
    echo "The provided version is 'dev-main', check ignored.\n";
    exit(0);
}

// Compare versions
if ($currentVersion === $inputVersion) {
    echo "The current version in composer.json matches the input version: $currentVersion\n";
    exit(0);
} else {
    echo "The current version in composer.json ($currentVersion) does not match the input version: $inputVersion\n";
    exit(1);
}