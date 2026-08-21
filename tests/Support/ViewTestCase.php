<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\Support;

use Closure;
use ErrorException;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * Renders package views through a real {@see WebView} with the same common parameters the
 * production injections provide (`$translator`, `$flash`, `$csrf`), under a strict error handler:
 * any undefined variable or array key inside a template is converted to an exception, so a view
 * consuming a parameter its caller does not pass fails loudly instead of emitting broken HTML.
 */
abstract class ViewTestCase extends TestCase
{
    use MatchesSnapshots;

    private const array FLASH_EMPTY = ['success' => null, 'warning' => null];

    /**
     * Renders a view with {@see Fixtures::for()} data and locks the resulting markup in a snapshot.
     *
     * @param string $name View name relative to `views/`, e.g. `session/login`.
     * @param array $overrides Parameter overrides merged on top of the base fixture.
     * @param Closure(string): string|null $normalize Extra output normalizer for nondeterministic
     * markup, applied before snapshot comparison only.
     */
    protected function assertViewSnapshot(string $name, array $overrides = [], ?Closure $normalize = null): void
    {
        $html = $this->renderView($name, Fixtures::for($name, $overrides));

        self::assertNotSame('', trim($html), "Rendering \"$name\" produced no output.");
        foreach (self::normalizers() as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html);
        }
        $this->assertMatchesSnapshot($normalize === null ? $html : $normalize($html));
    }

    protected function renderView(string $name, array $params = [], ?array $flash = null, array $common = []): string
    {
        $view = TestConfig::webView();
        $view->setParameters([
            'translator' => new PassthroughTranslator(),
            'flash' => $flash ?? self::FLASH_EMPTY,
            'csrf' => new Csrf('test-csrf-token', '_csrf', 'X-Csrf-Token'),
            ...$common,
        ]);

        set_error_handler(
            static function (int $severity, string $message, string $file, int $line): bool {
                throw new ErrorException($message, 0, $severity, $file, $line);
            },
            E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE,
        );

        try {
            return $view->render($name, $params);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Widget-generated identifiers that differ on every render (e.g. Bootstrap5 alerts).
     *
     * @return array<string, string>
     */
    private static function normalizers(): array
    {
        return [
            '/alert-\d+/' => 'alert-fixed',
        ];
    }
}
