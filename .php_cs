<?php

use PhpCsFixer\Config;
use Symfony\Component\Finder\Finder;

$finder = Finder::create()
                ->in(__DIR__ . '/plugin/src')
                ->name('*.php')
                ->exclude([
                    'node_modules',
                    'vendor',
                ])
                ->ignoreDotFiles(true)
                ->ignoreVCS(true);

return Config::create()
                ->setRules([
                    '@PSR2' => true,
                    'array_indentation' => true,
                    'array_syntax' => [
                        'syntax' => 'short',
                    ],
                    'binary_operator_spaces' => true,
                    'blank_line_after_namespace' => true,
                    'blank_line_before_return' => true,
                    'braces' => true,
                    'class_attributes_separation' => true,
                    'class_definition' => true,
                    'dir_constant' => true,
                    'logical_operators' => true,
                    'method_chaining_indentation' => true,
                    'no_multiline_whitespace_before_semicolons' => true,
                    'no_short_echo_tag' => true,
                    'no_spaces_around_offset' => true,
                    'no_unused_imports' => true,
                    'not_operator_with_successor_space' => true,
                    'no_whitespace_before_comma_in_array' => true,
                    'ordered_imports' => [
                        'sortAlgorithm' => 'alpha',
                    ],
                    'trailing_comma_in_multiline_array' => true,
                    'trim_array_spaces' => true,
                ])
                ->setFinder($finder);
