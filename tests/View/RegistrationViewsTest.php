<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class RegistrationViewsTest extends ViewTestCase
{
    public function testConnectSnapshot(): void
    {
        $this->assertViewSnapshot('registration/connect');
    }

    public function testRegisterSnapshot(): void
    {
        $this->assertViewSnapshot('registration/register');
    }

    public function testResendSnapshot(): void
    {
        $this->assertViewSnapshot('registration/resend');
    }
}
