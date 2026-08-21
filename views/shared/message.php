<?php

declare(strict_types=1);

use YiiRocks\Voyti\Helper\LinkButtonHelper;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var array{title: string, homeUrl: string} $data
 * @var TranslatorInterface $translator
 */

$this->setTitle($data['title']);

echo Html::div(
    Html::div(
        Html::H1($data['title'])
        . Html::a($translator->translate('voyti.view.go_home'), $data['homeUrl'])
            ->class(LinkButtonHelper::submitButtonClass()),
    )->class('card-body', 'text-center', 'py-5')->render(),
)->class('card', 'shadow-sm')->render();
