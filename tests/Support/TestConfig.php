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
            homeRoute: '/',
            enableRegistration: true,
            enableEmailConfirmation: true,
            allowPasswordRecovery: true,
            allowAdminPasswordRecovery: true,
            allowAccountDelete: true,
            emailChangeConfirmation: EmailChangeConfirmation::NONE,
            rememberLoginLifespan: 3600,
            tokenConfirmationLifespan: 3600,
            tokenRecoveryLifespan: 3600,
            enableSwitchIdentities: false,
            mailAdminOnRegister: null,
            recaptchaVersion: RecaptchaVersion::V2,
            maxPasswordAge: 0,
            enablePasswordComplexity: false,
            passwordHistoryLimit: 3,
            administratorPermissionName: 'voyti/admin',
            profileVisibility: ProfileVisibility::ADMIN,
            enableAuditLog: true,
            rememberMeCookieDomain: null,
            viewPath: null,
            mailPath: '/mail',
            enableRecommendations: false,
            accountMenuItems: [],
            privacyMenuItems: [],
            viewsPackagePaths: [dirname(__DIR__, 2) . '/views'],
        );
    }

    public static function webView(): WebView
    {
        return (new WebView())->withBasePath(dirname(__DIR__, 2) . '/views');
    }
}
