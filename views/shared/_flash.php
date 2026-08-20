<?php

declare(strict_types=1);

use YiiRocks\ToastBootstrap5\ToastInterface;
use Yiisoft\Bootstrap5\Alert;
use Yiisoft\Bootstrap5\AlertVariant;
use Yiisoft\View\WebView;

/**
 * Renders voyti's flash messages as Bootstrap 5 toasts (if yiirocks/toast-bootstrap5 is installed)
 * or alerts (fallback). Expects $flash and $toast to be injected.
 *
 * @var array{success: string|null, warning: string|null} $flash
 * @var ToastInterface|null $toast
 * @var WebView $this
 */

// Toast-bootstrap5 installed: render as toasts
if (isset($toast) && $toast instanceof ToastInterface) {
    echo $toast->render($this);
    return;
}

// Fallback: render as alerts
if ($flash['warning'] !== null) {
    echo Alert::widget()->body($flash['warning'])->variant(AlertVariant::WARNING)->render();
}

if ($flash['success'] !== null) {
    echo Alert::widget()->body($flash['success'])->variant(AlertVariant::SUCCESS)->render();
}
