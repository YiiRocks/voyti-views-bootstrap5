<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\ToastBootstrap5\ToastInterface;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;
use Yiisoft\View\WebView;

final class SharedViewsTest extends ViewTestCase
{
    public function testAdminLayoutSnapshot(): void
    {
        $this->assertViewSnapshot('shared/admin_layout');
    }

    public function testAdminMenuSnapshot(): void
    {
        $this->assertViewSnapshot('shared/_admin-menu');
    }

    public function testFlashEmptyProducesNoOutput(): void
    {
        self::assertSame('', $this->renderView('shared/_flash'));
    }

    public function testFlashRenderedAsToastWhenToastPackageInstalled(): void
    {
        $toast = new class implements ToastInterface {
            public function render(WebView $view): string
            {
                return '<div data-voyti-test="toast">toast markup</div>';
            }
        };

        $html = $this->renderView('shared/_flash', common: ['toast' => $toast]);

        self::assertStringContainsString('data-voyti-test="toast"', $html);
        self::assertStringNotContainsString('alert-warning', $html);
    }

    public function testFlashWarningAndSuccessAsAlerts(): void
    {
        $html = $this->renderView('shared/_flash', [], [
            'success' => 'Saved successfully',
            'warning' => 'Something is off',
        ]);

        self::assertStringContainsString('alert-warning', $html);
        self::assertStringContainsString('Something is off', $html);
        self::assertStringContainsString('alert-success', $html);
        self::assertStringContainsString('Saved successfully', $html);
    }

    public function testMenuSnapshot(): void
    {
        $this->assertViewSnapshot('shared/_menu');
    }

    public function testMessageSnapshot(): void
    {
        $this->assertViewSnapshot('shared/message');
    }

    public function testSwitchIdentitySnapshot(): void
    {
        $this->assertViewSnapshot('shared/_switch-identity');
    }

    public function testViewProfileSnapshot(): void
    {
        $this->assertViewSnapshot('shared/view_profile');
    }
}
