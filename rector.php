<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

include_once __DIR__ . '/php_min_version.php';

return static function (RectorConfig $rectorConfig): void {

    # load min version from centralized file
    $minVersion = (int)getenv('PHP_MIN_VERSION_RECTOR');

    if (!$minVersion) {
        die('PHP_MIN_VERSION_RECTOR environment variable not set or invalid.');
    }

    $rectorConfig->phpVersion($minVersion);


    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/devops/scripts',
        __DIR__ . '/src',
        __DIR__ . '/tests/phpunit',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->sets([
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::STRICT_BOOLEANS,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
    ]);

};
