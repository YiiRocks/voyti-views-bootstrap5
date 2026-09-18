<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var array{createdAt: string, actorLabel: string, action: string, targetLabel: string, context: string} $data
 */

echo Html::div()
    ->class('row py-2 border-bottom align-items-center')
    ->content(
        Html::div($data['createdAt'])->class('col-2'),
        Html::div($data['actorLabel'])->class('col-2'),
        Html::div($data['action'])->class('col-2 text-break'),
        Html::div($data['targetLabel'])->class('col-2 text-break'),
        Html::div($data['context'])->class('col-4 text-break small'),
    );
