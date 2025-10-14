<?php

$header = <<<'HEADER'
This file is part of the AutoFormBundle package.

(c) David ALLIX <http://a2lix.fr>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;


$finder = (new PhpCsFixer\Finder())
    ->in(['src', 'tests']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRules([
        '@autoPHPMigration:risky' => true,
        '@autoPHPMigration' => true,
        '@autoPHPUnitMigration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,

        'header_comment' => ['header' => $header],
        'class_attributes_separation' => ['elements' => ['method' => 'one']],
        'class_definition' => ['inline_constructor_arguments' => true],
        // 'date_time_immutable' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline', 'attribute_placement' => 'ignore'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'multiline_promoted_properties' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true],
        'nullable_type_declaration_for_default_null_value' => true,
        'numeric_literal_separator' => true,
        'operator_linebreak' => ['only_booleans' => true, 'position' => 'beginning'],
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
        'php_unit_data_provider_name' => true,
        'php_unit_data_provider_return_type' => true,
        'php_unit_data_provider_static' => true,
        'php_unit_dedicate_assert' => ['target' => 'newest'],
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'phpdoc_array_type' => true,
        'phpdoc_list_type' => true,
        'phpdoc_param_order' => true,
        'phpdoc_to_property_type' => ['scalar_types' => true],
        'phpdoc_to_return_type' => ['scalar_types' => true],
        'phpdoc_var_without_name' => true,
        'phpdoc_to_comment' => false,
        'single_line_throw' => true,
        'statement_indentation' => true,
        'trailing_comma_in_multiline' => ['after_heredoc' => true, 'elements' => ['arrays', 'parameters']],
        'use_arrow_functions' => true,
        'void_return' => true,

        PhpCsFixerCustomFixers\Fixer\ClassConstantUsageFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\ConstructorEmptyBracesFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\CommentSurroundedBySpacesFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\DeclareAfterOpeningTagFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\EmptyFunctionBodyFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\MultilineCommentOpeningClosingAloneFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoDoctrineMigrationsGeneratedCommentFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoDuplicatedArrayKeyFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoUselessCommentFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoUselessDirnameCallFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoUselessDoctrineRepositoryCommentFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoUselessParenthesisFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoUselessStrlenFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\NoUselessWriteVisibilityFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpUnitAssertArgumentsOrderFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpUnitNoUselessReturnFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpUnitRequiresConstraintFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpdocNoSuperfluousParamFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpdocSelfAccessorFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpdocTypesCommaSpacesFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpdocTypesTrimFixer::name() => true,
        // PhpCsFixerCustomFixers\Fixer\FunctionParameterSeparationFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpdocPropertySortedFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PromotedConstructorPropertyFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\ReadonlyPromotedPropertiesFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\SingleSpaceAfterStatementFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\SingleSpaceBeforeStatementFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\StringableInterfaceFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\TrimKeyFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\TypedClassConstantFixer::name() => true,
    ])
    ->setFinder($finder)
;
