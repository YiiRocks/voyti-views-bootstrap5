# Voyti Views Bootstrap5

[![Packagist Version](https://img.shields.io/packagist/v/yiirocks/voyti-views-bootstrap5.svg)](https://packagist.org/packages/yiirocks/voyti-views-bootstrap5)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/yiirocks/voyti-views-bootstrap5.svg)](https://php.net/)
[![Packagist](https://img.shields.io/packagist/dt/yiirocks/voyti-views-bootstrap5.svg)](https://packagist.org/packages/yiirocks/voyti-views-bootstrap5)
[![GitHub](https://img.shields.io/github/license/yiirocks/voyti-views-bootstrap5.svg)](https://github.com/yiirocks/voyti-views-bootstrap5/blob/main/LICENSE.md)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/yiirocks/voyti-views-bootstrap5/build.yml?branch=main)](https://github.com/yiirocks/voyti-views-bootstrap5/actions)

Stats for Nerds

[![Coverage](https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fyiirocks%2Fvoyti-views-bootstrap5%2Fbadges%2Fcoverage.json)](https://github.com/yiirocks/voyti-views-bootstrap5/tree/badges)
[![Tests](https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fyiirocks%2Fvoyti-views-bootstrap5%2Fbadges%2Ftests.json)](https://github.com/yiirocks/voyti-views-bootstrap5/tree/badges)
[![Assertions](https://img.shields.io/endpoint?url=https%3A%2F%2Fraw.githubusercontent.com%2Fyiirocks%2Fvoyti-views-bootstrap5%2Fbadges%2Fassertions.json)](https://github.com/yiirocks/voyti-views-bootstrap5/tree/badges)

---

Bootstrap5 HTML views for [Voyti](https://www.yii.rocks/voyti/) user management library: login, registration, account settings, profile, admin dashboard, and RBAC admin interfaces.

Provides the virtual package `yiirocks/voyti-views`, allowing future view implementations (Tailwind, etc.) as alternative packages.

## Installation

```bash
composer require yiirocks/voyti-views-bootstrap5
```

This package implements the `yiirocks/voyti-views` interface required by Voyti. No additional setup needed.

## Testing

Every view is rendered in a PHPUnit test with a strict error handler and locked in a markup snapshot, so contract drift between controllers and views fails CI instead of surfacing at runtime:

```bash
composer phpunit
```

Snapshots live next to their test classes in `tests/View/__snapshots__/`. After an intentional view change, regenerate them and review the diff:

```bash
UPDATE_SNAPSHOTS=true vendor/bin/phpunit
```

A smoke test renders every file under `views/`; adding a view without registering its fixture data in `tests/Support/Fixtures.php` fails the suite.
