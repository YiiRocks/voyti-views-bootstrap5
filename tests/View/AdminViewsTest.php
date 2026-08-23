<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class AdminViewsTest extends ViewTestCase
{
    public function testAuditLogIndexSnapshot(): void
    {
        $this->assertViewSnapshot('admin/audit-log/index');
    }

    public function testDashboardSnapshot(): void
    {
        $this->assertViewSnapshot('admin/dashboard/index');
    }

    public function testDashboardWithoutRecentActivity(): void
    {
        $base = Fixtures::for('admin/dashboard/index')['data'];
        $base['recentAuditLogs'] = [];
        $base['recommendedPackages'] = [];

        $html = $this->renderView('admin/dashboard/index', ['data' => $base]);

        self::assertStringContainsString('voyti.view.dashboard.no_recent_activity', $html);
        self::assertStringNotContainsString('voyti.view.dashboard.recommended_addons', $html);
    }

    public function testRbacCreateSnapshot(): void
    {
        $this->assertViewSnapshot('admin/rbac/create');
    }

    public function testRbacCreateWithErrors(): void
    {
        $base = Fixtures::for('admin/rbac/create')['data'];
        $base['errors'] = ['name' => ['Name is required.']];

        $html = $this->renderView('admin/rbac/create', [
            'data' => $base,
            'form' => Fixtures::for('admin/rbac/create')['form'],
        ]);

        self::assertStringContainsString('alert-danger', $html);
        self::assertStringContainsString('Name is required.', $html);
    }

    public function testRbacIndexSnapshot(): void
    {
        $this->assertViewSnapshot('admin/rbac/index');
    }

    public function testRbacRuleCreateWithErrors(): void
    {
        $base = Fixtures::for('admin/rbac/rule/create')['data'];
        $base['errors'] = ['name' => ['Name is required.']];

        $html = $this->renderView('admin/rbac/rule/create', [
            'data' => $base,
            'form' => Fixtures::for('admin/rbac/rule/create')['form'],
        ]);

        self::assertStringContainsString('alert-danger', $html);
        self::assertStringContainsString('Name is required.', $html);
    }

    public function testRbacRuleUpdateWithErrors(): void
    {
        $base = Fixtures::for('admin/rbac/rule/update')['data'];
        $base['errors'] = ['name' => ['Name is required.']];

        $html = $this->renderView('admin/rbac/rule/update', [
            'data' => $base,
            'form' => Fixtures::for('admin/rbac/rule/update')['form'],
        ]);

        self::assertStringContainsString('alert-danger', $html);
        self::assertStringContainsString('Name is required.', $html);
    }

    public function testRbacUpdateSnapshot(): void
    {
        $this->assertViewSnapshot('admin/rbac/update');
    }

    public function testRbacUpdateWithErrors(): void
    {
        $base = Fixtures::for('admin/rbac/update')['data'];
        $base['errors'] = ['name' => ['Name is required.']];

        $html = $this->renderView('admin/rbac/update', [
            'data' => $base,
            'form' => Fixtures::for('admin/rbac/update')['form'],
        ]);

        self::assertStringContainsString('alert-danger', $html);
        self::assertStringContainsString('Name is required.', $html);
    }

    public function testRuleCreateSnapshot(): void
    {
        $this->assertViewSnapshot('admin/rbac/rule/create');
    }

    public function testRuleIndexSnapshot(): void
    {
        $this->assertViewSnapshot('admin/rbac/rule/index');
    }

    public function testRuleUpdateSnapshot(): void
    {
        $this->assertViewSnapshot('admin/rbac/rule/update');
    }

    public function testUserAccountFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/_account');
    }

    public function testUserAssignmentsFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/_assignments');
    }

    public function testUserCreateSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/create');
    }

    public function testUserCreateWithCheckedRoleSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/create', [
            'data' => array_merge(Fixtures::for('admin/user/create')['data'], [
                'items' => [['name' => 'viewer', 'checked' => true]],
            ]),
        ]);
    }

    public function testUserIndexSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/index');
    }

    public function testUserIndexWithConfirmActionSnapshot(): void
    {
        $users = Fixtures::for('admin/user/index')['data']['users'];
        $users[0]['showConfirmAction'] = true;

        $this->assertViewSnapshot('admin/user/index', [
            'data' => array_merge(Fixtures::for('admin/user/index')['data'], [
                'users' => $users,
            ]),
        ]);
    }

    public function testUserInfoFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/_info');
    }

    public function testUserProfileFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/_profile');
    }

    public function testUserSessionsFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('admin/user/_sessions');
    }
}
