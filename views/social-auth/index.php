<?php

declare(strict_types=1);

use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\AuthClient\Widget\AuthChoice;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * @var WebView $this
 * @var array{
 *   menuHtml: string,
 *   flashHtml: string,
 *   accounts: list<array{formSubmitUrl: string, providerTitle: string, identity: string}>,
 *   authChoice: AuthChoice|null,
 * } $data
 * @var TranslatorInterface $translator
 * @var array{success: string|null, warning: string|null} $flash
 * @var Csrf $csrf
 */

$this->setTitle($translator->translate('voyti.view.social_auth.title', category: 'voyti-social-auth'));

echo Html::div()->open();
echo $data['menuHtml'];
echo $data['flashHtml'];

echo Html::H1($translator->translate('voyti.view.social_auth.title', category: 'voyti-social-auth'));

if (empty($data['accounts'])) {
    echo Html::p($translator->translate('voyti.view.social_auth.no_accounts', category: 'voyti-social-auth'));
} else {
    $items = [];
    foreach ($data['accounts'] as $account) {
        $disconnect = Html::form()
            ->post($account['formSubmitUrl'])
            ->csrf($csrf)
            ->open()
            . Field::buttonGroup()
                ->buttonsData([
                    [$translator->translate('voyti.view.disconnect_button', category: 'voyti-social-auth'), 'type' => 'submit', 'class' => 'btn btn-outline-danger btn-sm', 'tabindex' => 1],
                ])
                ->render()
            . Html::form()->close();

        $content = Html::div()->class('d-flex justify-content-between align-items-center gap-3')->open();
        $content .= Html::div()->class('d-flex align-items-center gap-2')->open();
        $content .= Html::strong($account['providerTitle'])->render();
        $content .= ' - ';
        $content .= Html::span($account['identity'], ['class' => 'text-muted'])->render();
        $content .= Html::div()->close();
        $content .= $disconnect;
        $content .= Html::div()->close();

        $items[] = Html::li($content)->class('list-group-item')->encode(false);
    }

    echo Html::ul()
        ->items(...$items)
        ->class('list-group')
        ->render();
}

if ($data['authChoice'] !== null && $data['authChoice']->getClients() !== []) {
    echo Html::div()->class('text-center mt-4')->open();
    echo $data['authChoice']->render();
    echo Html::div()->close();
}

echo Html::div()->close();
