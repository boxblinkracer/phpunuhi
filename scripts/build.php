<?php

$pharName = 'phpunuhi.phar';

$srcRoot = __DIR__ . "/..";
$buildRoot = __DIR__ . "/../build";


echo ">> building " . $pharName . "\n";

# delete old build directory
# and create new one again
deleteDirectory($buildRoot);
mkdir($buildRoot);


$phar = new Phar($buildRoot . "/" . $pharName, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $pharName);

# embed all php files from directory
$phar->buildFromDirectory($srcRoot, '/.$/');

# set stub to index.php file
$phar->setStub($phar->createDefaultStub("src/index.php"));

echo ">> build complete...";


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
