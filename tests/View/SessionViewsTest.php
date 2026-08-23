<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class SessionViewsTest extends ViewTestCase
{
    public function testLogin(): void
    {
        $html = $this->renderView('session/login', Fixtures::for('session/login'));

        self::assertStringContainsString('<form method="POST" action="/fixture/login"', $html);
        self::assertStringContainsString('name="login[login]"', $html);
        self::assertStringContainsString('name="login[password]"', $html);
        self::assertStringContainsString('name="login[rememberMe]"', $html);
        self::assertStringContainsString('href="/fixture/forgot"', $html);
        self::assertStringContainsString('href="/fixture/register"', $html);
    }

    public function testLoginWithEmptySocialClients(): void
    {
        $authChoice = Fixtures::authChoice();
        $authChoice->setClients([]);

        $html = $this->renderView('session/login', Fixtures::for('session/login', [
            'data' => [
                'formSubmitUrl' => '/fixture/login',
                'forgotPasswordUrl' => '/fixture/forgot',
                'showRegisterLink' => true,
                'registerUrl' => '/fixture/register',
                'recaptchaFieldHtml' => '',
                'authChoice' => $authChoice,
            ],
        ]));

        self::assertStringNotContainsString('voyti.view.login.social_divider', $html);
    }

    public function testLoginWithoutRegisterLink(): void
    {
        $html = $this->renderView('session/login', Fixtures::for('session/login', [
            'data' => [
                'formSubmitUrl' => '/fixture/login',
                'forgotPasswordUrl' => '/fixture/forgot',
                'showRegisterLink' => false,
                'registerUrl' => '/fixture/register',
                'recaptchaFieldHtml' => '',
                'authChoice' => null,
            ],
        ]));

        self::assertStringNotContainsString('/fixture/register"', $html);
    }

    public function testLoginWithSocialClients(): void
    {
        $html = $this->renderView('session/login', Fixtures::for('session/login', [
            'data' => [
                'formSubmitUrl' => '/fixture/login',
                'forgotPasswordUrl' => '/fixture/forgot',
                'showRegisterLink' => true,
                'registerUrl' => '/fixture/register',
                'recaptchaFieldHtml' => '',
                'authChoice' => Fixtures::authChoice(),
            ],
        ]));

        self::assertStringContainsString('voyti.view.login.social_divider', $html);
        self::assertStringContainsString('href="/fixture/voyti/session-auth"', $html);
        self::assertStringContainsString('class="auth-icon github"', $html);
    }
}
