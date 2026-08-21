<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class AccountViewsTest extends ViewTestCase
{
    public function testSessionsEmpty(): void
    {
        $html = $this->renderView('account/sessions', Fixtures::for('account/sessions', [
            'data' => ['menu' => Fixtures::menu(), 'sessions' => []],
        ]));

        self::assertStringContainsString('voyti.view.sessions.none', $html);
    }

    public function testSessionsSnapshot(): void
    {
        $this->assertViewSnapshot('account/sessions');
    }

    public function testUpdateSnapshot(): void
    {
        $this->assertViewSnapshot('account/update');
    }
}
