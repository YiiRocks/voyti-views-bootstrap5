<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * @var WebView $this
 * @var array{
 *   id: int,
 *   username: string,
 *   email: string,
 *   statusLabel: string,
 *   statusBadgeClass: string,
 *   showConfirmAction: bool,
 *   showForcePasswordChangeAction: bool,
 *   showSwitchIdentityAction: bool,
 *   switchIdentityDisabled: bool,
 *   showUrl: string,
 *   updateUrl: string,
 *   updateProfileUrl: string,
 *   sessionsUrl: string,
 *   confirmUrl: string,
 *   forcePasswordChangeUrl: string,
 *   passwordResetUrl: string,
 *   switchIdentityUrl: string,
 *   blockToggleUrl: string,
 *   blockToggleLabel: string,
 *   deleteUrl: string,
 * } $data
 * @var TranslatorInterface $translator
 * @var Csrf $csrf
 */

$menuItems = [
    Html::li(Html::a($translator->translate('voyti.view.update_link'), $data['updateUrl'])->class('dropdown-item')),
    Html::li(Html::a($translator->translate('voyti.view.update_profile_link'), $data['updateProfileUrl'])->class('dropdown-item')),
    Html::li(Html::a($translator->translate('voyti.view.admin.sessions_link'), $data['sessionsUrl'])->class('dropdown-item')),
];

if ($data['showConfirmAction']) {
    $menuItems[] = Html::li(
        Html::form()
            ->post($data['confirmUrl'])
            ->csrf($csrf)
            ->content(Html::submitButton($translator->translate('voyti.view.confirm_button'))->class('dropdown-item')->attribute('tabindex', 1)),
    );
}

if ($data['showForcePasswordChangeAction']) {
    $menuItems[] = Html::li(
        Html::form()
            ->post($data['forcePasswordChangeUrl'])
            ->csrf($csrf)
            ->content(Html::submitButton($translator->translate('voyti.view.force_password_change_button'))->class('dropdown-item')->attribute('tabindex', 1)),
    );
}

$menuItems[] = Html::li(
    Html::form()
        ->post($data['passwordResetUrl'])
        ->csrf($csrf)
        ->content(Html::submitButton($translator->translate('voyti.view.reset_password_button'))->class('dropdown-item')->attribute('tabindex', 1)),
);

if ($data['showSwitchIdentityAction']) {
    $menuItems[] = Html::li(
        Html::form()
        ->post($data['switchIdentityUrl'])
            ->csrf($csrf)
            ->content(Html::submitButton($translator->translate('voyti.view.admin.impersonate_button'))
                ->class('dropdown-item')
                ->attribute('tabindex', 1)
                ->disabled($data['switchIdentityDisabled'])),
    );
}

$menuItems[] = Html::li(Html::hr()->class('dropdown-divider'));
$menuItems[] = Html::li(
    Html::form()
        ->post($data['blockToggleUrl'])
        ->csrf($csrf)
        ->content(Html::submitButton($data['blockToggleLabel'])->class('dropdown-item', 'text-warning')->attribute('tabindex', 1)),
);
$menuItems[] = Html::li(
    Html::form()
        ->post($data['deleteUrl'])
        ->csrf($csrf)
        ->content(Html::submitButton($translator->translate('voyti.view.delete_button'))->class('dropdown-item', 'text-danger')->attribute('tabindex', 1)),
);

echo Html::div()
    ->class('row py-2 border-bottom align-items-center')
    ->content(
        Html::div($data['id'])->class('col-1'),
        Html::div($data['username'])->class('col-3 text-break'),
        Html::div($data['email'])->class('col-3 text-break'),
        Html::div(Html::span($data['statusLabel'])->class('badge', $data['statusBadgeClass']))->class('col-2'),
        Html::div()
            ->class('col-3 text-end')
            ->content(
                Html::a($translator->translate('voyti.view.info_link'), $data['showUrl'])->class('btn', 'btn-sm', 'btn-outline-secondary', 'me-1'),
                Html::div()
                    ->class('dropdown', 'd-inline-block')
                    ->content(
                        Html::button($translator->translate('voyti.view.actions_header'))
                            ->type('button')
                            ->class('btn', 'btn-sm', 'btn-outline-secondary', 'dropdown-toggle')
                            ->attribute('data-bs-toggle', 'dropdown')
                            ->attribute('aria-expanded', 'false'),
                        Html::tag(
                            'ul',
                            implode('', array_map(static fn(Stringable $item): string => (string) $item, $menuItems)),
                            ['class' => 'dropdown-menu dropdown-menu-end'],
                        )->encode(false),
                    ),
            ),
    );
