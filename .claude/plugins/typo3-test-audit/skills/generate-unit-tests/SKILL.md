---
name: generate-unit-tests
description: Reads the test-audit-*.txt report for a TYPO3 extension and processes every class in the Unit and Edge sections — no class is skipped. If a test file already exists it only checks for hint comments (TODO, markTestIncomplete, etc.) and adds the missing test cases if any are found; otherwise it leaves the file untouched. If no test file exists it generates a new test class with 2–3 test cases per public method. Use when the user asks to "generate unit tests", "write unit tests", "create tests from audit report".
argument-hint: <extension-path-or-name>
---

# Generate PHPUnit Unit Tests

## Step 1 – Resolve paths & environment

Resolve `ext_root` (dir with `Classes/`, `Tests/`, `composer.json`): absolute → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`. No argument → ask. Store `project_root` (dir with `vendor/`).

```bash
find "$ext_root" -maxdepth 1 -name "test-audit-*.txt" | sort
php --version 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/phpunit/phpunit/composer.json" 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/typo3/cms-core/composer.json" 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/typo3/testing-framework/composer.json" 2>/dev/null | head -1
find "$ext_root/Build/phpunit" "$ext_root" -maxdepth 2 \( -name "UnitTests.xml" -o -name "phpunit.xml" -o -name "phpunit.xml.dist" \) 2>/dev/null | head -1
```

No report → suggest `test-audit-text` first, stop. Multiple reports → ask (default: last alphabetically).

| Condition | Rule |
|-----------|------|
| PHPUnit ≥ 10 | `#[Test]` attribute |
| PHPUnit < 10 | `/** @test */` docblock |
| testing-framework present | `use TYPO3\TestingFramework\Core\Unit\UnitTestCase;` |
| testing-framework absent | `use PHPUnit\Framework\TestCase;` |

---

## Step 2 – Collect & check targets

Parse TXT sections `[Unit]` and `[Edge]` for all classes. Deduplicate → `target_files[]`. **Every entry must be processed — none may be skipped.**

```bash
for f in "${target_files[@]}"; do
  out="$ext_root/Tests/Unit/${f#Classes/}"; out="${out%.php}Test.php"
  test -f "$out" && echo "EXISTS:$out" || echo "MISSING:$out"
done
```

---

## Step 3 – Process each class

Path mapping: `Classes/Utility/Foo.php` → `Tests/Unit/Utility/FooTest.php`

### EXISTS → extend only if hints found

1. Read test file; search for: `// TODO`, `markTestIncomplete`, `markTestSkipped`, empty `// Arrange` / `// Act` / `// Assert`, empty method body `{}`.
2. **No hints → do nothing.** Status: `⏭️ skipped (no hints)`.
3. **Hints found →** read source class; write missing test case body (AAA) for each hint. Touch nothing else. Status: `✏️ extended (N completed)`.

### MISSING → create

1. Read source: namespace, class name, constructor, all `public function` (skip trivial getters/setters).
2. Plan **2–3 tests per public method**: happy path · boundary (null/empty/zero/max) · alternative branch.
3. `mkdir -p` if needed. Note `.php_`/`.php.bak`/`.php.disabled` variants in summary.
4. Write using the skeleton below. Status: `✅ created`.

**Skeleton (no dependencies):**
```php
<?php declare(strict_types=1);
namespace {test_namespace};

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use {source_namespace}\{ClassName};

final class {ClassName}Test extends UnitTestCase
{
    private {ClassName} $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new {ClassName}({constructor_args});
    }
    {test_methods}
}
```

**With dependencies** — add per dependency:
- `private DependencyType&MockObject $mockDep;`
- `$this->mockDep = $this->createMock(DependencyType::class);` in `setUp()`

**ViewHelper pattern:**
```php
private RenderingContextInterface&Stub $renderingContext;
private VariableProviderInterface&Stub $variableProvider;

protected function setUp(): void
{
    parent::setUp();
    $this->variableProvider = $this->createStub(VariableProviderInterface::class);
    $this->renderingContext = $this->createStub(RenderingContextInterface::class);
    $this->renderingContext->method('getVariableProvider')->willReturn($this->variableProvider);
    $this->subject = new {ClassName}();
    $this->subject->setRenderingContext($this->renderingContext);
    $this->subject->initializeArguments();
}
// Tests call: $this->subject->setArguments([...]); $result = $this->subject->render();
```

**GeneralUtility pattern:**
```php
protected function setUp(): void { ...; GeneralUtility::addInstance(SomeService::class, $this->mockService); }
protected function tearDown(): void { GeneralUtility::purgeInstances(); parent::tearDown(); }
```

**`createSubject()` helper:**
```php
/** @param array<string, mixed> $arguments */
private function createSubject(array $arguments): {ClassName} { ... }
```

**Test method format:**
```php
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
```

---

## Step 4 – Summary

**Always output in English.**

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
| 5 | **"Does not throw":** `$this->expectNotToPerformAssertions()` at top, no `assertTrue(true)` |
| 6 | **Tautological assertions:** never assert a statically-known type. `assertInstanceOf(Foo::class, $x)` when `$x: Foo` → assert the value instead (count, format, message). `assertIsString($s)` when `$s: string` → use `assertNotEmpty`/`assertSame`/`assertStringContainsString`. `assertIsCallable($c)` when `$c: Closure` → remove. **Backed enum:** `assertSame(Enum::CASE, Enum::from('val'))` and `assertSame('val', Enum::from('val')->value)` are both narrowed — test `from()` throws for invalid input via `expectException(\ValueError::class)` instead. **Inheritance:** `assertInstanceOf(Parent::class, $x)` when `$x: Child extends Parent` → use `(new \ReflectionClass(Child::class))->isSubclassOf(Parent::class)`. Remove any assertion PHPStan flags as `staticMethod.alreadyNarrowedType` or `staticMethod.impossibleType`. |
| 7 | **`DebuggerUtility::var_dump()`:** returns `''` when `inline=false`. Tests asserting the returned string must pass `inline: true`. |
| 8 | Final classes → `new`, never mock/stub final; `willReturn()` must match return type; no `->with()` on stub without `expects()`; **shared `setUp()` property**: declare `&Stub` + `createStub()` if *no* test method calls `expects()` on it — declare `&MockObject` + `createMock()` only if *at least one* test method calls `expects()`, then add `#[AllowMockObjectsWithoutExpectations]` on every method that does not; `purgeInstances()` in `tearDown()` when `addInstance()` used; never mock the class under test |
| 9 | **Naming:** `returnsEmptyStringForEmptyInput`, `throwsExceptionForNegativeValue` — never `test1`, `works` |
| 10 | **Assertions:** `assertSame` (scalar) · `assertStringContainsString` · `assertTrue/False` · `assertNull` · `expectException` · `assertCount` · `assertInstanceOf` · `expects()->method()->with()` |
