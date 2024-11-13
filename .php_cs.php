<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'array_syntax' => ['syntax' => 'short'],
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'no_empty_phpdoc' => true,
        'no_superfluous_elseif' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types_order' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        # -------------------------------------------------
        'not_operator_with_successor_space' => false,
        'no_superfluous_phpdoc_tags' => false,               # this would always remove required mixed annotations
        'yoda_style' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['./src/vendor'])
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests/phpunit')
    );