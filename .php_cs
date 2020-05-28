<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sortAlgorithm' => 'length'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'single_blank_line_before_namespace' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'blank_line_after_opening_tag' => true,
        'compact_nullable_typehint' => true,
        'declare_equal_normalize' => ['space' => 'none'],
        'function_typehint_space' => true,
        'new_with_braces' => true,
        'no_empty_statement' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'single_trait_insert_per_statement' => true,
        'class_attributes_separation' => ['elements' => ['method']],
    ]);
