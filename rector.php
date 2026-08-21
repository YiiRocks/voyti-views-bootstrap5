<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Yiisoft\CodeStyle\Rector\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/tests',
        __DIR__ . '/views',
    ])
    ->withPhpSets(php83: true)
    ->withSets([
        SetList::YII_CORE,
    ]);
