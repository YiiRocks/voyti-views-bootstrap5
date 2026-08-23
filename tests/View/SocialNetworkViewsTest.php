<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class SocialNetworkViewsTest extends ViewTestCase
{
    public function testIndexSnapshot(): void
    {
        $this->assertViewSnapshot('social-network/index');
    }

    public function testIndexWithConnectableClientsSnapshot(): void
    {
        $this->assertViewSnapshot('social-network/index', [
            'data' => array_merge(Fixtures::for('social-network/index')['data'], [
                'authChoice' => Fixtures::authChoice(),
            ]),
        ]);
    }

    public function testIndexWithoutAccounts(): void
    {
        $html = $this->renderView('social-network/index', Fixtures::for('social-network/index', [
            'data' => [
                'menuHtml' => '',
                'flashHtml' => '',
                'accounts' => [],
                'authChoice' => null,
            ],
        ]));

        self::assertStringContainsString('voyti.view.networks.no_networks', $html);
    }
}
