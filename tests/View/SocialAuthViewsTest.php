<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class SocialAuthViewsTest extends ViewTestCase
{
    public function testIndexSnapshot(): void
    {
        $this->assertViewSnapshot('social-auth/index');
    }

    public function testIndexWithConnectableClientsSnapshot(): void
    {
        $this->assertViewSnapshot('social-auth/index', [
            'data' => array_merge(Fixtures::for('social-auth/index')['data'], [
                'authChoice' => Fixtures::authChoice(),
            ]),
        ]);
    }

    public function testIndexWithoutAccounts(): void
    {
        $html = $this->renderView('social-auth/index', Fixtures::for('social-auth/index', [
            'data' => [
                'menuHtml' => '',
                'flashHtml' => '',
                'accounts' => [],
                'authChoice' => null,
            ],
        ]));

        self::assertStringContainsString('voyti.view.social_auth.no_accounts', $html);
    }
}
