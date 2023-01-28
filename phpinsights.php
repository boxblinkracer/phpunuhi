<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;

return [
    'preset' => 'symfony',
    'ide' => 'phpstorm',
    'exclude' => [
        '.export',
        '.reports',
        '.github',
        'bin',
        'build',
        'docker',
        'scripts',
        'tests',
        'vendor',
    ],
    'requirements' => [
        'min-quality' => 95,
        'min-complexity' => 90,
        'min-architecture' => 95,
        'min-style' => 95,
        'disable-security-check' => false,
    ],
    'add' => [
    ],
    'remove' => [
        SingleQuoteFixer::class,
        CastSpacesFixer::class,
        \PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer::class,
        \PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\DocCommentSniff::class,
        \PhpCsFixer\Fixer\Basic\BracesFixer::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\DocCommentAlignmentSniff::class,
        \PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\InlineCommentSniff::class,
        \PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer::class,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer::class,
        \SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class,
        \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff::class,
        \SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        \SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff::class,
        \SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\NullTypeHintOnLastPositionSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class,
    ],
    'config' => [
    ],
];
