<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class PasswordResetViewsTest extends ViewTestCase
{
    public function testConfirmSnapshot(): void
    {
        $this->assertViewSnapshot('password-reset/confirm');
    }

    public function testRequestSnapshot(): void
    {
        $this->assertViewSnapshot('password-reset/request');
    }
}
