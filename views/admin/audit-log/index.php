<?php

declare(strict_types=1);

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

/**
 * @var WebView $this
 * @var array{
 *   menu: list<array{label: string, url: string, alignEnd: bool, routeName: string|null}>,
 *   filterActionUrl: string,
 *   filters: array{actorUserId: string, targetUserId: string, action: string},
 *   paginator: OffsetPaginator,
 *   itemView: string,
 *   urlCreator: callable(array, array): string,
 * } $data
 * @var TranslatorInterface $translator
 * @var array{success: string|null, warning: string|null} $flash
 */

$this->setTitle($translator->translate('voyti.view.audit_log.title'));

echo Html::div()->open();
echo $this->render('../../shared/_admin-menu', ['menu' => $data['menu']]);
echo $this->render('../../shared/_flash');

echo Html::H1($translator->translate('voyti.view.audit_log.title'));

echo Html::form()
    ->action($data['filterActionUrl'])
    ->method('get')
    ->open();

$tabindex = 0;

echo Html::div()->class('row mb-3 g-2')->open();
echo Html::div()->class('col')->open();
echo Html::input('text')->class('form-control')->name('actorUserId')->value($data['filters']['actorUserId'])->addAttributes(['placeholder' => $translator->translate('voyti.view.audit_log.actor_header')])->attribute('tabindex', ++$tabindex);
echo Html::div()->close();

echo Html::div()->class('col')->open();
echo Html::input('text')->class('form-control')->name('targetUserId')->value($data['filters']['targetUserId'])->addAttributes(['placeholder' => $translator->translate('voyti.view.audit_log.target_header')])->attribute('tabindex', ++$tabindex);
echo Html::div()->close();

echo Html::div()->class('col')->open();
echo Html::input('text')->class('form-control')->name('action')->value($data['filters']['action'])->addAttributes(['placeholder' => $translator->translate('voyti.view.audit_log.action_header')])->attribute('tabindex', ++$tabindex);
echo Html::div()->close();

echo Html::div()->class('col-auto')->open();
echo Field::buttonGroup()
    ->containerClass('btn-group')
    ->buttonsData([
        [$translator->translate('voyti.view.reset_button'), 'type' => 'reset', 'class' => 'btn btn-outline-secondary', 'tabindex' => $tabindex + 2],
        [$translator->translate('voyti.view.filter_button'), 'type' => 'submit', 'class' => 'btn btn-outline-secondary', 'tabindex' => ++$tabindex],
    ]);
echo Html::div()->close();
echo Html::div()->close();

echo Html::form()->close();

echo Html::div()->class('d-none d-md-flex row fw-bold border-bottom pb-2 mb-2')->open();
echo Html::div($translator->translate('voyti.view.audit_log.created_header'))->class('col-2');
echo Html::div($translator->translate('voyti.view.audit_log.actor_header'))->class('col-2');
echo Html::div($translator->translate('voyti.view.audit_log.action_header'))->class('col-2');
echo Html::div($translator->translate('voyti.view.audit_log.target_header'))->class('col-2');
echo Html::div($translator->translate('voyti.view.audit_log.context_header'))->class('col-4');
echo Html::div()->close();

echo ListView::widget(constructorArguments: [$translator])
    ->dataReader($data['paginator'])
    ->containerTag(null)
    ->listTag(null)
    ->itemTag(null)
    ->summaryTemplate(null)
    ->layout('{items}{pager}')
    ->separator('')
    ->accessibility()
    ->urlCreator($data['urlCreator'])
    ->paginationWidget(
        OffsetPagination::widget(),
    )
    ->itemView($data['itemView'])
    ->render();
echo Html::div()->close();
