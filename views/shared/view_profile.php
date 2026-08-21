<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var array{
 *     displayName: string,
 *     gravatarUrl: string|null,
 *     showAdminFields: bool,
 *     email: string|null,
 *     registeredDisplay: string|null,
 *     statusLabel: string|null,
 *     statusBadgeClass: string|null,
 *     publicEmail: string|null,
 *     location: string|null,
 *     website: string|null,
 *     timezone: string|null,
 *     bio: string|null,
 *     profilePreviewClass: string,
 * } $profile
 * @var TranslatorInterface $translator
 */

$items = [];

$items[] = Html::li(
    Html::h3(Html::encode($profile['displayName']))->class('h4 mb-3')
    . ($profile['gravatarUrl'] !== null ? Html::img($profile['gravatarUrl'])->class('rounded-circle') : ''),
)->class('list-group-item', 'text-center py-3')->encode(false);

if ($profile['showAdminFields']) {
    $items[] = Html::li(
        Html::b($translator->translate('voyti.view.email_label'))->render() . ': '
        . Html::encode((string) $profile['email']),
    )->class('list-group-item', 'list-group-item-primary')->encode(false);

    $items[] = Html::li(
        Html::b($translator->translate('voyti.view.admin.registered_label'))->render() . ': '
        . $profile['registeredDisplay'],
    )->class('list-group-item', 'list-group-item-primary')->encode(false);

    $items[] = Html::li(
        Html::b($translator->translate('voyti.view.status_header'))->render() . ': '
        . Html::span($profile['statusLabel'])->class('badge', $profile['statusBadgeClass'])->render(),
    )->class('list-group-item', 'list-group-item-primary')->encode(false);
}

$items[] = Html::li(
    Html::b($translator->translate('voyti.view.public_email_label'))->render() . ': '
    . ($profile['publicEmail'] !== null ? Html::encode($profile['publicEmail']) : Html::span($translator->translate('voyti.view.not_set'))->class('text-muted fst-italic')),
)->class('list-group-item')->encode(false);

$items[] = Html::li(
    Html::b($translator->translate('voyti.view.location_label'))->render() . ': '
    . ($profile['location'] !== null ? Html::encode($profile['location']) : Html::span($translator->translate('voyti.view.not_set'))->class('text-muted fst-italic')),
)->class('list-group-item')->encode(false);

$items[] = Html::li(
    Html::b($translator->translate('voyti.view.website_label'))->render() . ': '
    . ($profile['website'] !== null ? Html::a(Html::encode($profile['website']), $profile['website'])->rel('noopener noreferrer')->target('_blank') : Html::span($translator->translate('voyti.view.not_set'))->class('text-muted fst-italic')),
)->class('list-group-item')->encode(false);

$items[] = Html::li(
    Html::b($translator->translate('voyti.view.timezone_label'))->render() . ': '
    . ($profile['timezone'] !== null ? Html::encode($profile['timezone']) : Html::span($translator->translate('voyti.view.not_set'))->class('text-muted fst-italic')),
)->class('list-group-item')->encode(false);

$items[] = Html::li(
    Html::b($translator->translate('voyti.view.bio_label'))->render() . ': '
    . ($profile['bio'] !== null ? nl2br(Html::encode($profile['bio'])) : Html::span($translator->translate('voyti.view.not_set'))->class('text-muted fst-italic')),
)->class('list-group-item')->encode(false);

echo Html::ul()
    ->items(...$items)
    ->class('list-group', $profile['profilePreviewClass'])
    ->render();
