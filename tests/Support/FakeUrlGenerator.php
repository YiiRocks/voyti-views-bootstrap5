<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\Support;

use Stringable;
use Yiisoft\Router\UrlGeneratorInterface;

/**
 * Generates deterministic placeholder URLs (`/fixture/<route-name>`) so rendered markup is stable
 * across runs.
 */
final class FakeUrlGenerator implements UrlGeneratorInterface
{
    public function generate(string $name, array $arguments = [], array $queryParameters = [], ?string $hash = null): string
    {
        return '/fixture/' . $name;
    }

    public function generateAbsolute(string $name, array $arguments = [], array $queryParameters = [], ?string $hash = null, ?string $scheme = null, ?string $host = null): string
    {
        return '/fixture/' . $name;
    }

    public function generateFromCurrent(array $replacedArguments, array $queryParameters = [], ?string $hash = null, ?string $fallbackRouteName = null): string
    {
        return '/fixture/current';
    }

    public function getUriPrefix(): string
    {
        return '';
    }

    public function setDefaultArgument(string $name, bool|float|int|string|Stringable|null $value): void {}

    public function setUriPrefix(string $name): void {}
}
