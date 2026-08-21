<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class ProfileViewsTest extends ViewTestCase
{
    public function testShowSnapshot(): void
    {
        $this->assertViewSnapshot('profile/show');
    }

    public function testUpdateSnapshot(): void
    {
        $this->assertViewSnapshot('profile/update');
    }
}
