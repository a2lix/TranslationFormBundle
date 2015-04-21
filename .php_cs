<?php

$header = <<<EOF
This file is part of A2lix projects.

(c) David ALLIX

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    // use default SYMFONY_LEVEL and extra fixers:
    ->fixers(array(
        'header_comment',
        'concat_with_spaces',
        'newline_after_open_tag',
        'ordered_use',
        'strict',
        'strict_param',
        'short_array_syntax',
        'php_unit_construct',
        'phpdoc_order'
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__)
    )
    ->setUsingCache(true)
;
