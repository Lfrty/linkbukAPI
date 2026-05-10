<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('storage');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,

        // si quieres algo más “flexible”
        'braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
        ],
    ])
    ->setFinder($finder);
