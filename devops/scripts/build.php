<?php

$pharName = 'phpunuhi.phar';

$srcRoot = __DIR__ . "/../..";
$buildRoot = __DIR__ . "/../../.build";


echo ">> building " . $pharName . "\n";

# delete old build directory
# and create new one again
deleteDirectory($buildRoot);
mkdir($buildRoot);


$phar = new Phar($buildRoot . "/" . $pharName, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $pharName);

$folder = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcRoot));
$items = array();
foreach ($folder as $item) {

    $filePath = $item->getPathname();
    $relativePath = str_replace($srcRoot . '/', '', $filePath);

    // Skip if the file or directory ends with "." or ".."
    if (preg_match('#(\.|\.\.)$#', $relativePath)) {
        continue;
    }

    $ignore = [
        '.idea/',
        '.github/',
        '.reports/',
        '.run/',
        '.svrunit/',
        'build',
        'devops/',
        'schema/',
        'scripts/',
        'tests/',
        '.gitignore',
        '.php_cs.php',
        '.phpstan.neon',
        'infection.js',
        'makefile',
        'php_min_version.php',
        'phparkitect.php',
        'phpunit.xml',
        'rector.php',
        'svrunit.xml',
    ];

    foreach ($ignore as $i) {
        if (startsWith($relativePath, $i)) {
            continue 2;
        }
    }

    $items[$relativePath] = $filePath;
}
$phar->buildFromIterator(new ArrayIterator($items));

# set stub to index.php file
$phar->setStub($phar->createDefaultStub("src/index.php"));


echo ">> build complete..." . PHP_EOL;


/**
 * @param $dir
 */
function deleteDirectory(string $dir): bool
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item === '.') {
            continue;
        }
        if ($item === '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

function startsWith(string $haystack, string $needle): bool
{
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}