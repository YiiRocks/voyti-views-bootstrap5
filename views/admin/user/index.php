<?php

declare(strict_types=1);

use YiiRocks\Voyti\Helper\LinkButtonHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * @var WebView $this
 * @var array{
 *   menu: list<array{label: string, url: string, alignEnd: bool, routeName: string|null}>,
 *   createUserUrl: string,
 *   filterActionUrl: string,
 *   filters: array{username: string, email: string, status: string},
 *   perPage: int,
 *   paginator: OffsetPaginator,
 *   urlCreator: callable(array, array): string,
 * } $data
 * @var TranslatorInterface $translator
 * @var array{success: string|null, warning: string|null} $flash
 * @var Csrf $csrf
 */

$this->setTitle($translator->translate('voyti.view.admin.title'));

echo Html::div()->open();
echo $this->render('../../shared/_admin-menu', ['menu' => $data['menu']]);
echo $this->render('../../shared/_flash');

echo Html::div()->class('d-flex justify-content-between align-items-center mb-3')->open();
echo Html::H1($translator->translate('voyti.view.admin.title'));
echo Html::a($translator->translate('voyti.view.admin.create_user_link'), $data['createUserUrl'])->class(LinkButtonHelper::submitButtonClass());
echo Html::div()->close();

echo Html::form()
    ->action($data['filterActionUrl'])
    ->method('get')
    ->open();

$tabindex = 0;

echo Html::div()->class('row mb-3 g-2')->open();
echo Html::div()->class('col')->open();
echo Html::input('text')->class('form-control')->name('username')->value($data['filters']['username'])->addAttributes(['placeholder' => $translator->translate('voyti.view.username_header')])->attribute('tabindex', ++$tabindex);
echo Html::div()->close();

echo Html::div()->class('col')->open();
echo Html::input('text')->class('form-control')->name('email')->value($data['filters']['email'])->addAttributes(['placeholder' => $translator->translate('voyti.view.email_header')])->attribute('tabindex', ++$tabindex);
echo Html::div()->close();

echo Html::div()->class('col')->open();
echo Html::select('status')
    ->class('form-select')
    ->prompt($translator->translate('voyti.view.status_header'))
    ->optionsData([
        'confirmed' => $translator->translate('voyti.view.status_active'),
        'unconfirmed' => $translator->translate('voyti.view.status_pending'),
        'blocked' => $translator->translate('voyti.view.status_blocked'),
    ])
    ->value($data['filters']['status'])
    ->attribute('tabindex', ++$tabindex);
echo Html::div()->close();

echo Html::div()->class('col-auto')->open();
echo Html::select('perPage')
    ->class('form-select')
    ->optionsData([
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
    ])
    ->value((string) $data['perPage'])
    ->attribute('aria-label', $translator->translate('voyti.view.per_page_label'))
    ->attribute('tabindex', ++$tabindex);
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
echo Html::div($translator->translate('voyti.view.id_header'))->class('col-1');
echo Html::div($translator->translate('voyti.view.username_header'))->class('col-3');
echo Html::div($translator->translate('voyti.view.email_header'))->class('col-3');
echo Html::div($translator->translate('voyti.view.status_header'))->class('col-2');
echo Html::div($translator->translate('voyti.view.actions_header'))->class('col-3 text-end');
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
    ->itemView(__DIR__ . '/_item')
    ->itemViewParameters(['csrf' => $csrf, 'translator' => $translator])
    ->render();
echo Html::div()->close();
