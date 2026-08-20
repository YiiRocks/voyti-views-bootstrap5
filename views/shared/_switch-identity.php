<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * Renders the "you're impersonating this user, click to restore" banner as a Bootstrap 5 alert.
 *
 * @var array{message: string, restoreUrl: string, restoreButtonLabel: string, csrfToken: string} $data
 * @var WebView $this
 */
?>
<?= Html::div()->class('alert alert-warning d-flex justify-content-between align-items-center')->open() ?>
<?= Html::span($data['message'])->render() ?>
<?= Html::form()->post($data['restoreUrl'])->csrf($data['csrfToken'])->open() ?>
<?= Html::submitButton($data['restoreButtonLabel'])->class('btn', 'btn-warning', 'btn-sm')->render() ?>
<?= Html::form()->close() ?>
<?= Html::div()->close() ?>
