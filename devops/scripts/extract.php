<?php

$phar = new Phar(__DIR__ . "/../../.build/phpunuhi.phar");

$phar->extractTo(__DIR__ . "/../../.build/content");
