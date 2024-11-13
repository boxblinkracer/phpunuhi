<?php

putenv('PHP_MIN_VERSION=7.4');
putenv('PHP_MIN_VERSION_RECTOR=70400');

if (isset($argc) && $argc > 1 && $argv[1] === '--echo') {
    echo getenv('PHP_MIN_VERSION');
}