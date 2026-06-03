# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a TYPO3 14.x demo project managed via Composer, running on DDEV (PHP 8.3, MariaDB 10.11, nginx). The focus of active development is the two local packages: **`hf-view-helpers`** (a TYPO3 Fluid ViewHelper collection) and **`hf-lib`** (a TYPO3 helper library), both under `packages/`.

## DDEV Environment

All PHP commands must run inside DDEV:

```bash
ddev start          # Start the environment
ddev stop           # Stop the environment
ddev ssh            # Shell into the web container
```

## Running Tests

Tests are run via DDEV using PHPUnit with the config at `packages/hf-view-helpers/Build/phpunit/UnitTests.xml`.

```bash
# Run all unit tests for hf-view-helpers
ddev exec php vendor/bin/phpunit -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml

# Run a single test file
ddev exec php vendor/bin/phpunit -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml packages/hf-view-helpers/Tests/Unit/Dummy/EmailTest.php

# Run a single test method
ddev exec php vendor/bin/phpunit -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml packages/hf-view-helpers/Tests/Unit/Dummy/GreeterTest.php --filter greetReturnsHelloWithName
```

### Coverage

```bash
ddev xdebug on
ddev exec php vendor/bin/phpunit \
  -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
  --coverage-html packages/hf-view-helpers/Build/phpunit/coverage
ddev xdebug off
```

## Static Analysis (PHPStan)

```bash
# Analyse production code (level 5)
ddev php vendor/bin/phpstan analyse -c packages/hf-view-helpers/phpstan.neon --memory-limit 10G

# Analyse test code (level 6, includes phpunit extension)
ddev php vendor/bin/phpstan analyse -c packages/hf-view-helpers/phpstan.tests.neon
```

## Architecture

### Package Structure

- `packages/hf-view-helpers/` — TYPO3 extension `hf_view_helpers` (`hausformat/hf-view-helpers`). ViewHelper namespace: `Hausformat\ViewHelpers\`. Test namespace: `Hausformat\ViewHelpers\Tests\`.
- `packages/hf-lib/` — TYPO3 extension `hf_lib` (`hausformat/hf-lib`). Provides base classes, services, utilities, repositories, and domain models used by `hf-view-helpers`.
- `packages/autoload/` — Composer autoload bootstrap files used by both packages during testing.

Each package has its own `.Build/vendor/` and `.Build/bin/` (set via `config.vendor-dir` in its `composer.json`) for standalone development. When running tests from the **root**, `vendor/bin/phpunit` and root-level `vendor/` are used instead.

### ViewHelper Organisation (`hf-view-helpers`)

ViewHelpers are grouped by domain under `Classes/ViewHelpers/`:

| Namespace | Purpose |
|-----------|---------|
| `Asset/` | Script and stylesheet inclusion with TYPO3 `CacheManager` |
| `Be/Form/`, `Be/Security/` | Backend form rendering and access-check conditions |
| `Cache/` | Adding TYPO3 frontend cache tags |
| `Debug/` | Cache tag introspection, duration display, rendering stop |
| `File/` | Filesystem existence checks |
| `Form/` | Extended Extbase form fields (select, upload) |
| `Format/` | String/number/date/JSON/HTML transformations |
| `Get/` | Deep property access and TSFE access |
| `Meta/` | `<meta>` tags and page title injection |
| `String/` | String utilities (explode) |
| `Uri/` | File URI generation |
| `Variable/` | Fluid template variable mutation |
| `Widget/` | Alphabetical pagination |

The `Classes/Dummy/` directory contains plain PHP classes (`Greeter`, `Email`, `ErrorHandler`, `Service`) that exist solely as a testing-infrastructure baseline — they are not part of the ViewHelper library's public API.

### Test Strategy

Tests extend `TYPO3\TestingFramework\Core\Unit\UnitTestCase`. All test methods use the `#[Test]` attribute and follow Arrange/Act/Assert structure. Tests for ViewHelpers that extend `AbstractViewHelper` can be written as unit tests when the `render()` logic is pure PHP with no TYPO3 subsystem calls.

ViewHelpers that inject TYPO3 infrastructure (`CacheManager`, `GlobalsService`, `makeInstance(...)`, `$GLOBALS['TSFE']`) require functional tests and are out of scope for the current unit test work.

The test audit document at `packages/hf-view-helpers/test-audit-hf-view-helpers.md` classifies every class as Z1 (no mocks needed), Z2 (mocks/stubs needed), functional-only, or not testable.

### Claude Plugin

A custom Claude plugin lives at `.claude/plugins/typo3-test-audit/`. Activate it with:

```bash
claude --plugin-dir .claude/plugins/typo3-test-audit
```
