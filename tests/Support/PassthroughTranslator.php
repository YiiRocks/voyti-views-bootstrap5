<?php

declare(strict_types=1);

namespace YiiRocks\VoytiViewsBootstrap5\Tests\Support;

use Stringable;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\TranslatorInterface;

/**
 * Returns the message ID for every lookup, so rendered markup is stable and independent of the
 * translation catalogs shipped by voyti and its optional packages.
 */
final class PassthroughTranslator implements TranslatorInterface
{
    public function addCategorySources(CategorySource ...$categories): static
    {
        return $this;
    }

    public function getLocale(): string
    {
        return 'en';
    }

    public function setLocale(string $locale): static
    {
        return $this;
    }

    public function translate(string|Stringable $id, array $parameters = [], ?string $category = null, ?string $locale = null): string
    {
        return (string) $id;
    }

    public function withDefaultCategory(string $category): static
    {
        return $this;
    }

    public function withLocale(string $locale): static
    {
        return $this;
    }
}
