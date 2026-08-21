<?php

declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use YiiRocks\Voyti\Service\SwitchIdentityService;
use YiiRocks\Voyti\VoytiConfig;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\FakeUrlGenerator;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\PassthroughTranslator;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\TestConfig;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Csrf\CsrfTokenInterface;
use Yiisoft\Csrf\StubCsrfToken;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Session;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Widget\WidgetFactory;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

require dirname(__DIR__) . '/vendor/autoload.php';

// Views resolve widgets through WidgetFactory's static factory: shared/_menu.php and
// shared/_admin-menu.php call SwitchIdentity::widget(), and _flash.php / RBAC create render
// Alert::widget(). Widget::widget() always rebuilds its widget from constructor arguments
// resolved through the factory, so the widget's dependencies must be defined here by class
// name. The impersonation banner itself is covered by voyti's own test suite; a guest identity
// repository keeps SwitchIdentity on its empty-string not-switched path.
$eventDispatcher = new class implements EventDispatcherInterface {
    public function dispatch(object $event): object
    {
        return $event;
    }
};

$switchIdentityService = new SwitchIdentityService(
    TestConfig::voyti(),
    new CurrentUser(
        new class implements IdentityRepositoryInterface {
            public function findIdentity(string $id): ?IdentityInterface
            {
                return null;
            }
        },
        $eventDispatcher,
    ),
    new Session(),
    $eventDispatcher,
);

WidgetFactory::initialize(null, [
    CsrfTokenInterface::class => new StubCsrfToken('test-csrf-token'),
    EventDispatcherInterface::class => $eventDispatcher,
    SessionInterface::class => new Session(),
    SwitchIdentityService::class => $switchIdentityService,
    TranslatorInterface::class => new PassthroughTranslator(),
    UrlGeneratorInterface::class => new FakeUrlGenerator(),
    VoytiConfig::class => TestConfig::voyti(),
    WebViewRenderer::class => TestConfig::viewRenderer(),
]);
