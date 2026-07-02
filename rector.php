<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/modules',
    ])
    ->withSkip([
        __DIR__ . '/modules/*/Database/Migrations',
        __DIR__ . '/modules/*/Database/Seeders',
        __DIR__ . '/modules/*/Database/Factories',
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: false,
    );
