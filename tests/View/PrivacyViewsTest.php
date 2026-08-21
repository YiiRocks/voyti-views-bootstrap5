<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class PrivacyViewsTest extends ViewTestCase
{
    public function testAnonymizeSnapshot(): void
    {
        $this->assertViewSnapshot('privacy/anonymize');
    }

    public function testDeleteSnapshot(): void
    {
        $this->assertViewSnapshot('privacy/delete');
    }

    public function testIndexSnapshot(): void
    {
        $this->assertViewSnapshot('privacy/index');
    }

    public function testIndexWithoutDeleteLink(): void
    {
        $html = $this->renderView('privacy/index', Fixtures::for('privacy/index', [
            'data' => [
                'menu' => Fixtures::menu(),
                'showDeleteLink' => false,
                'deleteUrl' => null,
                'privacyLinks' => [],
            ],
        ]));

        self::assertStringNotContainsString('voyti.view.privacy.delete_account', $html);
    }
}
