---
name: generate-unit-tests
description: Reads the test-audit-*.txt report for a TYPO3 extension and processes every class in the Unit and Edge sections — no class is skipped. If a test file already exists it only checks for hint comments (TODO, markTestIncomplete, etc.) and adds the missing test cases if any are found; otherwise it leaves the file untouched. If no test file exists it generates a new test class with broad coverage — one test per branch, boundary value, and exception path per public method. Use when the user asks to "generate unit tests", "write unit tests", "create tests from audit report".
argument-hint: <extension-path-or-name>
---

# Generate PHPUnit Unit Tests

## Step 1 – Resolve paths & environment

Resolve `ext_root` (dir with `Classes/`, `Tests/`, `composer.json`): absolute → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`. No argument → ask. Store `project_root` (dir with `vendor/`).

```bash
find "$ext_root" -maxdepth 1 -name "test-audit-*.txt" | sort
grep '"version"' "$project_root/vendor/phpunit/phpunit/composer.json" 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/typo3/testing-framework/composer.json" 2>/dev/null | head -1
```

No report → suggest `test-audit-text` first, stop. Multiple → ask (default: last alphabetically).
PHPUnit ≥ 10 → `#[Test]`; < 10 → `/** @test */`. testing-framework present → `UnitTestCase`; absent → `TestCase`.

---

## Step 2 – Collect & check targets

Parse `[Unit]` and `[Edge]` sections from the TXT. Deduplicate → `target_files[]`. **Every entry must be processed.**

```bash
for f in "${target_files[@]}"; do
  out="$ext_root/Tests/Unit/${f#Classes/}"; out="${out%.php}Test.php"
  test -f "$out" && echo "EXISTS:$out" || echo "MISSING:$out"
done
```

---

## Step 3 – Process each class

`Classes/Utility/Foo.php` → `Tests/Unit/Utility/FooTest.php`

### EXISTS → extend only if hints found

Search for `// TODO`, `markTestIncomplete`, `markTestSkipped`, empty AAA comments, empty `{}`. No hints → `⏭️ skipped`. Hints found → fill in missing test bodies (AAA), touch nothing else → `✏️ extended (N)`.

### MISSING → create (`✅ created`)

1. Read source: namespace, class name, constructor, all `public function` (skip trivial getters/setters).
2. Analyse every public method for all distinct code paths — generate a test for **each**:
   - **Happy path**, every `if/else`/`match`/`switch` arm, ternary, early-return
   - **Bool flags/arguments**: always test **both `true` and `false`** — never only the default
   - **All boundary values**: `null`, `''`, `0`, `-1`, `PHP_INT_MAX`, empty arrays
   - **Every exception path**: one test per reachable `throw` (use `expectException`)
   - **Data providers** (`#[DataProvider]`) when multiple inputs share the same assertion pattern
   - **No cap** on test count
3. `mkdir -p` if needed; write with the skeleton below.

**Skeleton:**
```php
<?php declare(strict_types=1);
namespace {test_namespace};

use TYPO3\TestingFramework\Core\Unit\UnitTestCase; // or PHPUnit\Framework\TestCase
use {source_namespace}\{ClassName};

final class {ClassName}Test extends UnitTestCase
{
    private {ClassName} $subject;
    // With deps: private DepType&MockObject $mockDep;  (or &Stub if no expects())

    protected function setUp(): void
    {
        parent::setUp();
        // With deps: $this->mockDep = $this->createMock(DepType::class);
        $this->subject = new {ClassName}({constructor_args});
    }

    #[Test]
    public function {camelCaseSentence}(): void
    {
        // Arrange
        $input = ...; $expected = ...;
        // Act
        $result = $this->subject->{method}($input);
        // Assert
        self::assertSame($expected, $result);
    }
}
```

**ViewHelper setUp:**
```php
$this->variableProvider = $this->createStub(VariableProviderInterface::class);
$this->renderingContext = $this->createStub(RenderingContextInterface::class);
$this->renderingContext->method('getVariableProvider')->willReturn($this->variableProvider);
$this->subject = new {ClassName}();
$this->subject->setRenderingContext($this->renderingContext);
$this->subject->initializeArguments();
// Tests: $this->subject->setArguments([...]); $result = $this->subject->render();
```

**ViewHelper `initializeArguments()` must have its own test** — calling it only as a setUp side-effect does not count as method coverage. Add:
```php
#[Test]
public function initializeArgumentsRegistersExpectedArguments(): void
{
    // Re-create subject so initializeArguments() is called inside the test body
    $subject = new {ClassName}();
    $subject->setRenderingContext($this->renderingContext);
    $subject->initializeArguments();
    $args = $subject->prepareArguments();
    self::assertArrayHasKey('{argName}', $args);
}
```

**GeneralUtility:** `addInstance(Svc::class, $mock)` in `setUp()`; `purgeInstances()` + `parent::tearDown()` in `tearDown()`.

---

## Step 4 – Summary (always in English)

```
## Generated Unit Tests
| Source class | Test file | Status | Notes |
|---|---|---|---|
| ... | ... | ✅ created / ✏️ extended (N) / ⏭️ skipped (no hints) | |

PHP: {version} · PHPUnit: {major}.x · TYPO3: {version} · testing-framework: {version}
```

---

## Quality rules

| # | Rule |
|---|------|
| 1 | `declare(strict_types=1)` · `final class` · all test methods `public function (): void` |
| 2 | AAA comments mandatory; omit `// Arrange` only if empty |
| 3 | `parent::setUp()` first; `parent::tearDown()` last |
| 4 | No `@covers`, no magic numbers, no `ConnectionPool`/`QueryBuilder` in unit tests |
| 5 | **"Does not throw":** `$this->expectNotToPerformAssertions()` at top; never `assertTrue(true)` |
| 6 | **No tautological assertions:** never assert a statically-known type (`assertIsString($s: string)`, `assertInstanceOf(Foo::class, $x: Foo)`, `assertIsCallable($c: Closure)`). Assert the *value* instead. For backed enums test that `from()` throws for invalid input via `expectException(\ValueError::class)`. Remove anything PHPStan flags as `alreadyNarrowedType` / `impossibleType`. |
| 7 | `DebuggerUtility::var_dump()` returns `''` when `inline=false`; pass `inline: true` to assert the returned string. |
| 8 | Final classes → `new`, never mock. Use `&Stub`/`createStub()` unless a test calls `expects()` — then `&MockObject`/`createMock()` + `#[AllowMockObjectsWithoutExpectations]` on non-expecting methods. Never `->with()` on stub without `expects()`. Never mock the subject. |
| 9 | **Naming:** `returnsEmptyStringForEmptyInput`, `throwsExceptionForNegativeValue` — never `test1`, `works` |
| 10 | **Assertions:** `assertSame` · `assertStringContainsString` · `assertTrue/False` · `assertNull` · `expectException` · `assertCount` · `assertInstanceOf` · `expects()->method()->with()` |
| 11 | **Coverage goal:** full branch coverage; no cap on test count. Prefer `#[DataProvider]` over near-duplicate methods. Every bool argument/flag must appear in tests as both `true` and `false`. ViewHelper `initializeArguments()` must be called inside a test body (not only in `setUp()`) to count as method coverage. |
