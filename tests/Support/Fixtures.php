<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\Support;

use LogicException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use YiiRocks\Voyti\Helper\LinkButtonHelper;
use YiiRocks\Voyti\Model\Form\Auth\LoginForm;
use YiiRocks\Voyti\Model\Form\Auth\RecoveryForm;
use YiiRocks\Voyti\Model\Form\Auth\RegistrationForm;
use YiiRocks\Voyti\Model\Form\Auth\ResendForm;
use YiiRocks\Voyti\Model\Form\Rbac\AuthItemForm;
use YiiRocks\Voyti\Model\Form\Rbac\RuleForm;
use YiiRocks\Voyti\Model\Form\Settings\ConsentForm;
use YiiRocks\Voyti\Model\Form\Settings\SettingsForm;
use YiiRocks\Voyti\Model\Form\Settings\UserProfileForm;
use YiiRocks\Voyti\TwoFactor\Form\ConfirmForm;
use YiiRocks\Voyti\TwoFactor\Form\TwoFactorCodeForm;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetLoader;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Factory\Factory;
use Yiisoft\Session\Session;
use Yiisoft\Yii\AuthClient\Client\GitHub;
use Yiisoft\Yii\AuthClient\Collection;
use Yiisoft\Yii\AuthClient\StateStorage\DummyStateStorage;
use Yiisoft\Yii\AuthClient\Widget\AuthChoice;

/**
 * Builds realistic render parameters for every view in the package, mirroring the `@var` contract
 * documented in each template's header docblock. {@see Fixtures::for()} is the single source both
 * the per-area snapshot tests and the all-views smoke test consume, so a new view without a
 * fixture fails the smoke test with a named error.
 */
final class Fixtures
{
    /**
     * Views that legitimately produce no output when rendered standalone.
     */
    private const array EMPTY_OUTPUT = ['privacy/export', 'shared/_flash'];

    /**
     * Builds a real {@see AuthChoice} widget configured exactly as both production call sites do
     * (voyti core's SessionController and voyti-social-auth's SocialAuthController), wired with
     * a GitHub OAuth2 client. Each call returns a fresh instance: the widget caches its rendered
     * open tag internally, so instances must never be reused across renders.
     */
    public static function authChoice(): AuthChoice
    {
        $root = dirname(__DIR__, 2);
        $aliases = new Aliases([
            '@assets' => "$root/vendor/yiisoft/yii-auth-client/resources/assets",
            '@assetsUrl' => '/assets/authclient',
            '@vendor' => "$root/vendor",
        ]);
        $httpClient = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new LogicException('Auth clients never hit the network while rendering views.');
            }
        };

        return (new AuthChoice(
            new Collection([
                'github' => new GitHub(
                    $httpClient,
                    new Psr17Factory(),
                    new DummyStateStorage(),
                    new Factory(),
                    new Session(),
                ),
            ]),
            new FakeUrlGenerator(),
            new AssetManager($aliases, new AssetLoader($aliases)),
        ))->authRoute('voyti/session-auth')
            ->linkAttributes(['class' => LinkButtonHelper::submitButtonClass()]);
    }

    public static function coreViews(): string
    {
        return dirname(__DIR__, 2) . '/views';
    }

    /**
     * @return array<string, mixed>
     */
    public static function for(string $name, array $overrides = []): array
    {
        $fixtures = self::all();
        if (!isset($fixtures[$name])) {
            self::failMissing($name);
        }

        return array_merge($fixtures[$name](), $overrides);
    }

    public static function hasFixture(string $name): bool
    {
        return isset(self::all()[$name]) || in_array($name, self::EMPTY_OUTPUT, true);
    }

    public static function isEmptyOutput(string $name): bool
    {
        return in_array($name, self::EMPTY_OUTPUT, true);
    }

    /**
     * @return list<array{label: string, url: string, alignEnd: bool, routeName: string|null}>
     */
    public static function menu(): array
    {
        return [
            ['label' => 'Account', 'url' => '/fixture/account', 'alignEnd' => false, 'routeName' => 'voyti/user-account'],
            ['label' => 'Logout', 'url' => '/fixture/logout', 'alignEnd' => true, 'routeName' => 'voyti/session-logout'],
        ];
    }

    public static function paginator(int $total): OffsetPaginator
    {
        $items = [];
        for ($i = 1; $i <= $total; $i++) {
            $items[] = ['id' => $i];
        }

        return (new OffsetPaginator(new IterableDataReader($items)))
            ->withPageSize(2)
            ->withCurrentPage(1);
    }

    /**
     * @return array{
     *     displayName: string,
     *     gravatarUrl: string|null,
     *     showAdminFields: bool,
     *     email: string|null,
     *     registeredDisplay: string|null,
     *     statusLabel: string|null,
     *     statusBadgeClass: string|null,
     *     publicEmail: string|null,
     *     location: string|null,
     *     website: string|null,
     *     timezone: string|null,
     *     bio: string|null,
     *     profilePreviewClass: string,
     * }
     */
    public static function profile(bool $showAdminFields = false): array
    {
        return [
            'displayName' => 'Jane Doe',
            'gravatarUrl' => 'https://gravatar.com/avatar/abc',
            'showAdminFields' => $showAdminFields,
            'email' => $showAdminFields ? 'jane@example.com' : null,
            'registeredDisplay' => $showAdminFields ? '2026-01-01 00:00' : null,
            'statusLabel' => $showAdminFields ? 'Active' : null,
            'statusBadgeClass' => $showAdminFields ? 'text-bg-success' : null,
            'publicEmail' => 'jane.public@example.com',
            'location' => 'Berlin',
            'website' => 'https://example.com',
            'timezone' => 'Europe/Berlin',
            'bio' => "Line one\nLine two",
            'profilePreviewClass' => '',
        ];
    }

    /**
     * @return array<string, Closure(): array<string, mixed>>
     */
    private static function all(): array
    {
        $translator = new PassthroughTranslator();

        return [
            'account/sessions' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'sessions' => [
                        [
                            'session' => [
                                'ip' => '203.0.113.10',
                                'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64)',
                                'lastSeenDisplay' => '2026-08-20 12:00',
                            ],
                            'isCurrentSession' => true,
                            'formSubmitUrl' => '/fixture/revoke-current',
                        ],
                        [
                            'session' => [
                                'ip' => '198.51.100.7',
                                'userAgent' => 'Mozilla/5.0 (Windows NT 10.0)',
                                'lastSeenDisplay' => '2026-08-19 09:30',
                            ],
                            'isCurrentSession' => false,
                            'formSubmitUrl' => '/fixture/revoke-other',
                        ],
                    ],
                ],
            ],

            'account/update' => static fn(): array => [
                'form' => new SettingsForm(TestConfig::voyti(), $translator),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/account-update',
                ],
            ],

            'admin/audit-log/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'filterActionUrl' => '/fixture/audit-log',
                    'filters' => ['actorUserId' => '', 'targetUserId' => '', 'action' => 'login'],
                    'logs' => [
                        [
                            'createdAt' => '2026-08-20 12:00',
                            'actorLabel' => 'jane',
                            'action' => 'login.success',
                            'targetLabel' => 'jane',
                            'context' => '{"ip":"203.0.113.10"}',
                        ],
                        [
                            'createdAt' => '2026-08-20 11:00',
                            'actorLabel' => 'admin',
                            'action' => 'user.block',
                            'targetLabel' => 'mallory',
                            'context' => '{}',
                        ],
                    ],
                    'paginator' => self::paginator(3),
                    'pageUrlPattern' => '/fixture/audit-log?page=:page',
                    'firstPageUrl' => '/fixture/audit-log',
                ],
            ],

            'admin/dashboard/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'tiles' => [
                        ['labelKey' => 'voyti.view.dashboard.tile_users', 'value' => 42, 'url' => '/fixture/users', 'borderClass' => 'border-primary'],
                        ['labelKey' => 'voyti.view.dashboard.tile_sessions', 'value' => 7, 'url' => '/fixture/sessions', 'borderClass' => 'border-info'],
                    ],
                    'trendWidgets' => [
                        [
                            'titleKey' => 'voyti.view.dashboard.trend_logins',
                            'periods' => [
                                ['labelKey' => 'voyti.view.dashboard.period_day', 'value' => 12, 'params' => []],
                                ['labelKey' => 'voyti.view.dashboard.period_week', 'value' => 84, 'params' => []],
                            ],
                        ],
                    ],
                    'recommendedPackages' => [
                        [
                            'packageName' => 'yiirocks/voyti-2fa',
                            'labelKey' => 'voyti.view.dashboard.package_2fa',
                            'descriptionKey' => 'voyti.view.dashboard.package_2fa_desc',
                            'composerUrl' => 'https://packagist.org/packages/yiirocks/voyti-2fa',
                            'docsUrl' => 'https://www.yii.rocks/',
                        ],
                    ],
                    'recentAuditLogs' => [
                        ['createdAt' => '2026-08-20 12:00', 'action' => 'login.success', 'targetLabel' => 'jane'],
                        ['createdAt' => '2026-08-20 11:00', 'action' => 'user.block', 'targetLabel' => 'mallory'],
                    ],
                    'auditLogUrl' => '/fixture/audit-log',
                ],
            ],

            'admin/rbac/create' => static fn(): array => [
                'form' => new AuthItemForm($translator, 'role'),
                'data' => [
                    'menu' => self::menu(),
                    'title' => 'Create role',
                    'formSubmitUrl' => '/fixture/rbac-create',
                    'children' => [
                        ['name' => 'childRole', 'checked' => true],
                        ['name' => 'otherRole', 'checked' => false],
                    ],
                    'errors' => [],
                ],
            ],

            'admin/rbac/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'title' => 'Roles',
                    'createLinkLabel' => 'Create role',
                    'createUrl' => '/fixture/rbac-create',
                    'filterUrl' => '/fixture/rbac',
                    'filterName' => '',
                    'filterDescription' => '',
                    'items' => [
                        [
                            'name' => 'admin',
                            'description' => 'Administrator role',
                            'childrenDisplay' => 'editor, viewer',
                            'updateUrl' => '/fixture/rbac-update',
                            'formSubmitUrl' => '/fixture/rbac-delete',
                        ],
                    ],
                ],
            ],

            'admin/rbac/rule/create' => static fn(): array => [
                'form' => new RuleForm($translator),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/rule-create',
                    'errors' => [],
                ],
            ],

            'admin/rbac/rule/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'createUrl' => '/fixture/rule-create',
                    'rules' => [
                        ['name' => 'ownRule', 'updateUrl' => '/fixture/rule-update', 'formSubmitUrl' => '/fixture/rule-delete'],
                    ],
                ],
            ],

            'admin/rbac/rule/update' => static fn(): array => [
                'form' => new RuleForm($translator),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/rule-update',
                    'errors' => [],
                ],
            ],

            'admin/rbac/update' => static fn(): array => [
                'form' => new AuthItemForm($translator, 'role'),
                'data' => [
                    'menu' => self::menu(),
                    'title' => 'Update role',
                    'formSubmitUrl' => '/fixture/rbac-update',
                    'children' => [
                        ['name' => 'childRole', 'checked' => true],
                    ],
                    'assignedUsers' => [
                        ['id' => '1', 'username' => 'jane'],
                    ],
                    'errors' => [],
                ],
            ],

            'admin/user/_account' => static fn(): array => [
                'form' => new SettingsForm(TestConfig::voyti(), $translator),
                'data' => [
                    'menu' => self::menu(),
                    'title' => 'Update user',
                    'formSubmitUrl' => '/fixture/admin-user-account',
                    'errors' => [],
                    'usernameValue' => 'jane',
                    'emailValue' => 'jane@example.com',
                    'items' => [
                        ['name' => 'admin', 'checked' => true],
                    ],
                ],
            ],

            'admin/user/_assignments' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/assignments',
                    'assignedItemNames' => ['admin'],
                    'availableItemNames' => ['editor', 'viewer'],
                ],
            ],

            'admin/user/_info' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'username' => 'jane',
                    'profile' => self::profile(showAdminFields: true),
                ],
            ],

            'admin/user/_profile' => static fn(): array => [
                'form' => new UserProfileForm($translator),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/admin-user-profile',
                    'timezoneOptions' => ['Europe/Berlin' => 'Europe/Berlin', 'UTC' => 'UTC'],
                ],
            ],

            'admin/user/_sessions' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'sessions' => [
                        [
                            'ip' => '203.0.113.10',
                            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64)',
                            'lastSeenDisplay' => '2026-08-20 12:00',
                            'isRevoked' => false,
                            'revokedAtDisplay' => null,
                        ],
                        [
                            'ip' => '198.51.100.7',
                            'userAgent' => 'Mozilla/5.0 (Windows NT 10.0)',
                            'lastSeenDisplay' => '2026-08-19 09:30',
                            'isRevoked' => true,
                            'revokedAtDisplay' => '2026-08-19 10:00',
                        ],
                    ],
                    'formSubmitUrl' => '/fixture/admin-terminate-sessions',
                ],
            ],

            'admin/user/create' => static fn(): array => [
                'form' => new RegistrationForm(TestConfig::voyti(), $translator),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/admin-user-create',
                    'usernameValue' => '',
                    'emailValue' => '',
                    'items' => [
                        ['name' => 'viewer', 'checked' => false],
                    ],
                    'errors' => [],
                ],
            ],

            'admin/user/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'createUserUrl' => '/fixture/admin-user-create',
                    'filterActionUrl' => '/fixture/admin-users',
                    'filters' => ['username' => '', 'email' => '', 'status' => 'confirmed'],
                    'perPage' => 10,
                    'users' => [
                        [
                            'id' => 1,
                            'username' => 'jane',
                            'email' => 'jane@example.com',
                            'statusLabel' => 'Active',
                            'statusBadgeClass' => 'text-bg-success',
                            'showConfirmAction' => false,
                            'showForcePasswordChangeAction' => true,
                            'showSwitchIdentityAction' => true,
                            'switchIdentityDisabled' => false,
                            'showUrl' => '/fixture/user-show',
                            'updateUrl' => '/fixture/user-update',
                            'updateProfileUrl' => '/fixture/user-profile',
                            'sessionsUrl' => '/fixture/user-sessions',
                            'confirmUrl' => '/fixture/user-confirm',
                            'forcePasswordChangeUrl' => '/fixture/user-force-change',
                            'passwordResetUrl' => '/fixture/user-reset',
                            'switchIdentityUrl' => '/fixture/user-switch',
                            'blockToggleUrl' => '/fixture/user-block',
                            'blockToggleLabel' => 'Block',
                            'deleteUrl' => '/fixture/user-delete',
                        ],
                    ],
                    'paginator' => self::paginator(1),
                    'pageUrlPattern' => '/fixture/admin-users?page=:page',
                    'firstPageUrl' => '/fixture/admin-users',
                ],
            ],

            'password-reset/confirm' => static fn(): array => [
                'form' => new RecoveryForm(TestConfig::voyti(), $translator, RecoveryForm::SCENARIO_RESET),
            ],

            'password-reset/request' => static fn(): array => [
                'form' => new RecoveryForm(TestConfig::voyti(), $translator, RecoveryForm::SCENARIO_REQUEST),
                'data' => [
                    'formSubmitUrl' => '/fixture/recovery-request',
                    'loginUrl' => '/fixture/login',
                    'recaptchaFieldHtml' => '',
                ],
            ],

            'privacy/anonymize' => static fn(): array => [
                'form' => new ConsentForm($translator, 'anonymize', 'voyti.view.privacy.anonymize_confirm_label', 'voyti-gdpr'),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/anonymize',
                ],
            ],

            'privacy/delete' => static fn(): array => [
                'form' => new ConsentForm($translator, 'delete-account', 'voyti.view.delete_account.confirm_label', 'voyti'),
                'data' => [
                    'menu' => self::menu(),
                    'formSubmitUrl' => '/fixture/delete-account',
                ],
            ],

            'privacy/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'showDeleteLink' => true,
                    'deleteUrl' => '/fixture/delete-account',
                    'privacyLinks' => [
                        ['label' => 'Export my data', 'url' => '/fixture/export'],
                    ],
                ],
            ],

            'profile/show' => static fn(): array => [
                'profile' => self::profile(),
            ],

            'profile/update' => static fn(): array => [
                'form' => new UserProfileForm($translator),
                'data' => [
                    'menu' => self::menu(),
                    'updateUrl' => '/fixture/profile-update',
                    'profile' => self::profile(),
                    'timezoneOptions' => ['Europe/Berlin' => 'Europe/Berlin', 'UTC' => 'UTC'],
                ],
            ],

            'registration/connect' => static fn(): array => [
                'data' => [
                    'providerTitle' => 'GitHub',
                    'loginUrl' => '/fixture/login',
                    'registerUrl' => '/fixture/register',
                ],
            ],

            'registration/register' => static fn(): array => [
                'form' => new RegistrationForm(TestConfig::voyti(), $translator),
                'data' => [
                    'formSubmitUrl' => '/fixture/register',
                    'loginUrl' => '/fixture/login',
                    'recaptchaFieldHtml' => '',
                ],
            ],

            'registration/resend' => static fn(): array => [
                'form' => new ResendForm(TestConfig::voyti(), $translator),
                'data' => [
                    'formSubmitUrl' => '/fixture/resend',
                    'recaptchaFieldHtml' => '',
                ],
            ],

            'session/login' => static fn(): array => [
                'form' => new LoginForm(TestConfig::voyti(), $translator),
                'data' => [
                    'formSubmitUrl' => '/fixture/login',
                    'forgotPasswordUrl' => '/fixture/forgot',
                    'showRegisterLink' => true,
                    'registerUrl' => '/fixture/register',
                    'recaptchaFieldHtml' => '',
                    'authChoice' => null,
                ],
            ],

            'settings/index' => static fn(): array => [
                'data' => [
                    'menu' => self::menu(),
                    'displayName' => 'Jane Doe',
                    'email' => 'jane@example.com',
                    'memberSinceDisplay' => '2026-01-01',
                ],
            ],

            'shared/_switch-identity' => static fn(): array => [
                'data' => [
                    'message' => 'Impersonating jane',
                    'restoreUrl' => '/fixture/restore',
                    'restoreButtonLabel' => 'Restore',
                    'csrfToken' => 'test-csrf-token',
                ],
            ],

            'shared/admin_layout' => static fn(): array => [
                'content' => '<p>Layout content</p>',
            ],

            'shared/message' => static fn(): array => [
                'data' => [
                    'title' => 'Operation complete',
                    'homeUrl' => '/',
                ],
            ],

            'social-auth/index' => static fn(): array => [
                'data' => [
                    'menuHtml' => '<ul class="nav nav-tabs mb-4"><li class="nav-item"><a class="nav-link" href="/fixture/account">Account</a></li></ul>',
                    'flashHtml' => '',
                    'accounts' => [
                        [
                            'formSubmitUrl' => '/fixture/disconnect',
                            'providerTitle' => 'GitHub',
                            'identity' => 'jane',
                        ],
                    ],
                    'authChoice' => null,
                ],
            ],

            'two-factor/_code-form' => static fn(): array => [
                'form' => new TwoFactorCodeForm($translator, 'totp'),
                'formSubmitUrl' => '/fixture/2fa-enable',
            ],

            'two-factor/_email' => static fn(): array => [
                'form' => new TwoFactorCodeForm($translator, 'email'),
                'data' => [
                    'emailCodeSent' => true,
                    'userEmail' => 'jane@example.com',
                    'sendCodeUrl' => '/fixture/2fa-email-send',
                    'enableUrl' => '/fixture/2fa-email-enable',
                ],
            ],

            'two-factor/_totp' => static fn(): array => [
                'form' => new TwoFactorCodeForm($translator, 'totp'),
                'data' => [
                    'qrCodeUri' => '<svg data-voyti-test="qr"></svg>',
                    'secret' => 'JBSWY3DPEHPK3PXP',
                    'renewLabel' => 'Renew secret',
                    'renewUrl' => '/fixture/2fa-totp-renew',
                    'renewErrorMessage' => 'Renewal failed',
                    'manualEntryLabel' => 'Manual entry:',
                    'formSubmitUrl' => '/fixture/2fa-totp-enable',
                ],
            ],

            'two-factor/_webauthn' => static fn(): array => [
                'data' => [
                    'registerUrl' => '/fixture/2fa-webauthn-register',
                    'requestOptions' => [
                        'publicKey' => [
                            'challenge' => 'dGVzdC1jaGFsbGVuZ2U',
                            'rp' => ['name' => 'Voyti Tests'],
                            'user' => ['id' => 'dXNlci0x', 'name' => 'jane', 'displayName' => 'Jane'],
                            'pubKeyCredParams' => [['type' => 'public-key', 'alg' => -7]],
                        ],
                    ],
                    'errorMessage' => 'Registration failed',
                ],
            ],

            'two-factor/_webauthn-confirm' => static fn(): array => [
                'data' => [
                    'formSubmitUrl' => '/fixture/2fa-webauthn-confirm',
                    'requestOptions' => [
                        'publicKey' => [
                            'challenge' => 'dGVzdC1jaGFsbGVuZ2U',
                            'allowCredentials' => [],
                        ],
                    ],
                    'errorMessage' => 'Assertion failed',
                ],
            ],

            'two-factor/backup-codes' => static fn(): array => [
                'coreViews' => self::coreViews(),
                'data' => [
                    'menu' => self::menu(),
                    'codes' => ['1234-5678', '8765-4321'],
                    'continueUrl' => '/fixture/settings',
                ],
            ],

            'two-factor/confirm' => static fn(): array => [
                'form' => new ConfirmForm($translator),
                'data' => [
                    'isCodeBased' => true,
                    'methodFragmentUrl' => null,
                    'formSubmitUrl' => '/fixture/2fa-confirm',
                ],
            ],

            'two-factor/index' => static fn(): array => [
                'coreViews' => self::coreViews(),
                'form' => new TwoFactorCodeForm($translator, 'totp'),
                'data' => [
                    'menu' => self::menu(),
                    'errors' => [],
                    'isEnabled' => false,
                    'method' => 'totp',
                    'enabledWithMethodMessage' => 'Enabled with totp',
                    'codeDelivered' => false,
                    'requiresCodeDelivery' => false,
                    'isCodeBased' => true,
                    'reauthFragmentUrl' => null,
                    'disableSendCodeUrl' => '/fixture/2fa-disable-send',
                    'disableUrl' => '/fixture/2fa-disable',
                    'hasBackupCodes' => true,
                    'regenerateBackupCodesUrl' => '/fixture/2fa-regenerate',
                    'methods' => [
                        ['name' => 'totp', 'label' => 'TOTP'],
                        ['name' => 'email', 'label' => 'Email'],
                    ],
                    'methodUrls' => ['totp' => '/fixture/2fa-method-totp', 'email' => '/fixture/2fa-method-email'],
                    'preloadedFragmentHtml' => null,
                    'autoloadUrl' => '/fixture/2fa-method-totp',
                ],
            ],

            'two-factor/unavailable' => static fn(): array => [
                'coreViews' => self::coreViews(),
                'data' => [
                    'menu' => self::menu(),
                ],
            ],

            'shared/view_profile' => static fn(): array => [
                'profile' => self::profile(showAdminFields: true),
            ],

            'shared/_menu' => static fn(): array => [
                'menu' => self::menu(),
            ],

            'shared/_admin-menu' => static fn(): array => [
                'menu' => self::menu(),
            ],
        ];
    }

    private static function failMissing(string $name): never
    {
        throw new LogicException(
            "No fixture registered for view \"$name\". Add an entry to Fixtures::all().",
        );
    }
}
