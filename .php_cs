<?php

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    // use default SYMFONY_LEVEL and extra fixers:
    ->fixers(array(
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
