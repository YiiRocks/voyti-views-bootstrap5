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
}
