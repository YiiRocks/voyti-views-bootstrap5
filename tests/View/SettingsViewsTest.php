<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class SettingsViewsTest extends ViewTestCase
{
    public function testIndexSnapshot(): void
    {
        $this->assertViewSnapshot('settings/index');
    }
}
