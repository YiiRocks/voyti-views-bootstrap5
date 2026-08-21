<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var array{
 *   menu: list<array{label: string, url: string, alignEnd: bool, routeName: string|null}>,
 *   tiles: list<array{labelKey: string, value: int, url: string, borderClass: string}>,
 *   trendWidgets: list<array{titleKey: string, periods: list<array{labelKey: string, value: int, params: array<string, int>}>}>,
 *   recommendedPackages: list<array{packageName: string, labelKey: string, descriptionKey: string, composerUrl: string, docsUrl: string}>,
 *   recentAuditLogs: list<array{createdAt: string, action: string, targetLabel: string}>,
 *   auditLogUrl: string,
 * } $data
 * @var TranslatorInterface $translator
 * @var array{success: string|null, warning: string|null} $flash
 */

$this->setTitle($translator->translate('voyti.view.dashboard.title'));

/**
 * Bootstrap 5's card markup (`.card` > `.card-header`, `.card-body`, `.card-footer`), built with
 * plain Html:: calls since yiisoft/bootstrap5 has no Card widget.
 */
$card = static function (string $body, ?string $header = null, ?string $footer = null, string $class = '', string $footerClass = ''): string {
    $html = Html::div()->class(trim('card ' . $class))->open();
    if ($header !== null) {
        $html .= Html::div($header)->class('card-header')->encode(false)->render();
    }
    $html .= Html::div($body)->class('card-body')->encode(false)->render();
    if ($footer !== null) {
        $html .= Html::div($footer)->class(trim('card-footer ' . $footerClass))->encode(false)->render();
    }
    $html .= Html::div()->close();
    return $html;
};

echo Html::div()->open();
echo $this->render('../../shared/_admin-menu', ['menu' => $data['menu']]);
echo $this->render('../../shared/_flash');

echo Html::H1($translator->translate('voyti.view.dashboard.title'));

echo Html::div()->class('row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 mb-4')->open();
foreach ($data['tiles'] as $tile) {
    echo Html::div()->class('col')->open();
    echo Html::a()
        ->href($tile['url'])
        ->class('text-decoration-none')
        ->open();
    echo $card(
        body: Html::div((string) $tile['value'])->class('fs-2 fw-bold')
            . Html::div($translator->translate($tile['labelKey']))->class('text-muted small'),
        class: 'h-100 text-center ' . $tile['borderClass'],
    );
    echo Html::a()->close();
    echo Html::div()->close();
}
echo Html::div()->close();

echo Html::div()->class('row row-cols-1 row-cols-md-2 g-3 mb-4')->open();
foreach ($data['trendWidgets'] as $widget) {
    echo Html::div()->class('col')->open();
    $bodyContent = Html::div()->class('row row-cols-3 text-center g-2')->open();
    foreach ($widget['periods'] as $period) {
        $bodyContent .= Html::div(
            Html::div((string) $period['value'])->class('fs-3 fw-bold')
            . Html::div($translator->translate($period['labelKey'], $period['params']))->class('text-muted small'),
        )->class('col')->encode(false);
    }
    $bodyContent .= Html::div()->close();
    echo $card(
        body: $bodyContent,
        header: (string) Html::H2($translator->translate($widget['titleKey']))->class('h5 mb-0'),
        class: 'h-100',
    );
    echo Html::div()->close();
}
echo Html::div()->close();

echo Html::a()->href($data['auditLogUrl'])->class('text-decoration-none')->open();
$auditContent = '';
if ($data['recentAuditLogs'] === []) {
    $auditContent = (string) Html::p($translator->translate('voyti.view.dashboard.no_recent_activity'))->class('text-muted mb-0');
} else {
    $lastKey = array_key_last($data['recentAuditLogs']);
    foreach ($data['recentAuditLogs'] as $key => $log) {
        $auditContent .= Html::div(
            Html::div($log['createdAt'])->class('col-3 col-md-2 text-muted small')
            . Html::div($log['action'])->class('col-9 col-md-4 text-break')
            . Html::div($log['targetLabel'])->class('col-12 col-md-6 text-break small text-muted'),
        )->class('row py-2 align-items-center' . ($key !== $lastKey ? ' border-bottom' : ''))->encode(false);
    }
}
echo $card(
    body: $auditContent,
    header: (string) Html::H2($translator->translate('voyti.view.dashboard.recent_activity'))->class('h5 mb-0'),
);
echo Html::a()->close();

if ($data['recommendedPackages'] !== []) {
    echo Html::H2($translator->translate('voyti.view.dashboard.recommended_addons'))->class('h5 mb-3 mt-4');
    echo Html::div()->class('row row-cols-1 row-cols-md-2 g-3')->open();
    foreach ($data['recommendedPackages'] as $package) {
        echo Html::div()->class('col')->open();
        echo $card(
            body: (string) Html::p($translator->translate($package['descriptionKey']))->class('text-muted small mb-0'),
            header: (string) Html::H3($translator->translate($package['labelKey']))->class('h6 mb-0'),
            footer: Html::a($translator->translate('voyti.view.dashboard.view_on_packagist'))
                ->href($package['composerUrl'])
                ->class('btn btn-sm btn-link')
                ->target('_blank')
                ->rel('noopener noreferrer')
            . ' '
            . Html::a($translator->translate('voyti.view.dashboard.documentation'))
                ->href($package['docsUrl'])
                ->class('btn btn-sm btn-link')
                ->target('_blank')
                ->rel('noopener noreferrer'),
            class: 'h-100',
            footerClass: 'bg-transparent',
        );
        echo Html::div()->close();
    }
    echo Html::div()->close();
}

echo Html::div()->close();
