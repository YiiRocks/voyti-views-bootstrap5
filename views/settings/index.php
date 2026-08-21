<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var array<string, mixed> $data
 * @var TranslatorInterface $translator
 * @var array{success: string|null, warning: string|null} $flash
 */

$this->setTitle($translator->translate('voyti.view.settings.dashboard_title'));

echo Html::div()->open();
echo $this->render('../shared/_menu', ['menu' => $data['menu']]);
echo $this->render('../shared/_flash');

echo Html::h1($translator->translate('voyti.view.settings.welcome', ['name' => $data['displayName']]))->class('h3 mb-3');

echo Html::ul()
    ->items(
        Html::li(
            Html::b($translator->translate('voyti.view.email_label'))->render() . ': '
            . Html::encode($data['email']),
        )->class('list-group-item')->encode(false),
        Html::li(
            Html::b($translator->translate('voyti.view.settings.member_since'))->render() . ': '
            . Html::encode($data['memberSinceDisplay']),
        )->class('list-group-item')->encode(false),
    )
    ->class('list-group', 'mb-4')
    ->render();

echo Html::div()->close();
