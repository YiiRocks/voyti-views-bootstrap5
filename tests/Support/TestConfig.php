<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\Support;

use Nyholm\Psr7\Factory\Psr17Factory;
use YiiRocks\Voyti\Enum\EmailChangeConfirmation;
use YiiRocks\Voyti\Enum\ProfileVisibility;
use YiiRocks\Voyti\Enum\RecaptchaVersion;
use YiiRocks\Voyti\VoytiConfig;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

/**
 * Central builders for the real value objects the views' dependencies need: a {@see VoytiConfig}
 * for form models and a {@see WebViewRenderer} for the SwitchIdentity widget.
 */
final class TestConfig
{
    private static ?VoytiConfig $voytiConfig = null;

    public static function viewRenderer(): WebViewRenderer
    {
        $psr17Factory = new Psr17Factory();

        return new WebViewRenderer(
            $psr17Factory,
            $psr17Factory,
            new Aliases(),
            self::webView(),
        );
    }

    public static function voyti(): VoytiConfig
    {
        return self::$voytiConfig ??= new VoytiConfig(
            appName: 'Voyti Views Tests',
            recaptchaVersion: RecaptchaVersion::V2,
            accountMenuItems: [],
            enableRegistration: true,
            enableEmailConfirmation: true,
            enablePasswordComplexity: false,
            enableRecommendations: false,
            enableSwitchIdentities: false,
            homeRoute: '/',
            mailAdminOnRegister: null,
            passwordHistoryLimit: 3,
            allowPasswordRecovery: true,
            allowAdminPasswordRecovery: true,
            allowAccountDelete: true,
            privacyMenuItems: [],
            emailChangeConfirmation: EmailChangeConfirmation::NONE,
            rememberLoginLifespan: 3600,
            tokenConfirmationLifespan: 3600,
            tokenRecoveryLifespan: 3600,
            administratorPermissionName: 'voyti/admin',
            profileVisibility: ProfileVisibility::ADMIN,
            maxPasswordAge: 0,
            viewPath: null,
            mailPath: '/mail',
            enableAuditLog: true,
            rememberMeCookieDomain: null,
        );
    }

    public static function webView(): WebView
    {
        return (new WebView())->withBasePath(dirname(__DIR__, 2) . '/views');
    }
}
