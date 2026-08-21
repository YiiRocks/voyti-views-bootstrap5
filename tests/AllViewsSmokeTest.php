<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests;

use FilesystemIterator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\Fixtures;
use YiiRocks\VoytiViewsBootstrap5\Tests\Support\ViewTestCase;

/**
 * Renders every view file in the package with its registered fixture. Any new view without a
 * fixture entry fails here with a named error, so the suite can never silently grow blind spots.
 */
final class AllViewsSmokeTest extends ViewTestCase
{
    public static function provideAllViews(): Generator
    {
        foreach (self::viewFiles() as $file) {
            $name = substr($file, strlen(dirname(__DIR__) . '/views/'), -4);
            yield $name => [$name];
        }
    }

    #[DataProvider('provideAllViews')]
    public function testRendersWithoutErrors(string $name): void
    {
        if (Fixtures::isEmptyOutput($name)) {
            self::assertSame('', trim($this->renderView($name)));
            return;
        }

        $html = $this->renderView($name, Fixtures::for($name));
        self::assertNotSame('', trim($html), "Rendering \"$name\" produced no output.");
    }

    /**
     * @return list<string>
     */
    private static function viewFiles(): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__DIR__) . '/views', FilesystemIterator::SKIP_DOTS),
        );

        $files = [];
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $files[] = $fileInfo->getPathname();
            }
        }
        sort($files);

        return $files;
    }
}
