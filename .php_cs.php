<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['./src/vendor'])
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests/phpunit')
    );