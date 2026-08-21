<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\View;

use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

final class TwoFactorViewsTest extends ViewTestCase
{
    public function testBackupCodesSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/backup-codes');
    }

    public function testCodeFormFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/_code-form');
    }

    public function testConfirmCodeBasedSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/confirm');
    }

    public function testConfirmWebauthnSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/confirm', [
            'data' => [
                'isCodeBased' => false,
                'methodFragmentUrl' => '/fixture/2fa-method-fragment',
                'formSubmitUrl' => '/fixture/2fa-confirm',
            ],
        ]);
    }

    public function testEmailFragmentAwaitingCodeSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/_email', [
            'data' => [
                'emailCodeSent' => false,
                'userEmail' => 'jane@example.com',
                'sendCodeUrl' => '/fixture/2fa-email-send',
                'enableUrl' => '/fixture/2fa-email-enable',
            ],
        ]);
    }

    public function testEmailFragmentCodeSentSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/_email');
    }

    public function testIndexDisabledWithAutoloadSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/index');
    }

    public function testIndexEnabledCodeBasedSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/index', [
            'data' => array_merge(Fixtures::for('two-factor/index')['data'], [
                'isEnabled' => true,
                'isCodeBased' => true,
                'requiresCodeDelivery' => false,
                'hasBackupCodes' => false,
            ]),
        ]);
    }

    public function testIndexEnabledWebauthnSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/index', [
            'data' => array_merge(Fixtures::for('two-factor/index')['data'], [
                'isEnabled' => true,
                'isCodeBased' => false,
                'reauthFragmentUrl' => '/fixture/2fa-reauth',
            ]),
        ]);
    }

    public function testTotpFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/_totp');
    }

    public function testUnavailableSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/unavailable');
    }

    public function testWebauthnConfirmFragmentSnapshot(): void
    {
        // The fragment embeds a per-render nonce in its script config; normalize it so the
        // snapshot only locks the deterministic markup around it.
        $this->assertViewSnapshot(
            'two-factor/_webauthn-confirm',
            normalize: static fn(string $html): string => preg_replace('/"nonce":"[0-9a-f]{16}"/', '"nonce":"FIXED"', $html),
        );
    }

    public function testWebauthnFragmentSnapshot(): void
    {
        $this->assertViewSnapshot('two-factor/_webauthn');
    }
}
