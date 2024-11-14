<?php

include_once __DIR__ . '/src/XmlStorage.php';

\PHPUnuhi\Bundles\Storage\StorageFactory::getInstance()->registerStorage(new XmlStorage());