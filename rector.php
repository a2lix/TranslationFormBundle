<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // ->withRootFiles()
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets()
    ->withAttributesSets(all: true)
    ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
;
