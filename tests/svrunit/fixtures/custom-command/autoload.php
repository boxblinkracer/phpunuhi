<?php

include_once __DIR__ . '/src/CustomCommand.php';

\PHPUnuhi\AppManager::registerExtensionCommand(new CustomCommand());