---
name: generate-unit-tests
description: Reads the test-audit-*.txt report for a TYPO3 extension and processes every class of the chosen group (Z1, Z2, or Both) across both Unit and Edge sections — no class is skipped. Optional second argument selects the group; defaults to Both. If a test file already exists it only checks for hint comments (TODO, markTestIncomplete, etc.) and adds the missing test cases if any are found; otherwise it leaves the file untouched. If no test file exists it generates a new test class with 2–3 test cases per public method. Use when the user asks to "generate unit tests", "write unit tests for Z1", "create tests for Z2 classes", "generate PHPUnit tests from audit report".
argument-hint: <extension-path-or-name> [Z1|Z2]
---

# Generate PHPUnit Unit Tests for Z1 / Z2 Classes

---

## Step 1 – Resolve paths and collect environment

Resolve `ext_root` (dir containing `Classes/`, `Tests/`, `composer.json`): absolute path → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`. If no argument, ask. Store `project_root` (dir containing `vendor/`).

Run in one bash block:

```bash
find "$ext_root" -maxdepth 1 -name "test-audit-*.txt" | sort
php --version 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/phpunit/phpunit/composer.json" 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/typo3/cms-core/composer.json" 2>/dev/null | head -1
grep '"version"' "$project_root/vendor/typo3/testing-framework/composer.json" 2>/dev/null | head -1
find "$ext_root/Build/phpunit" "$ext_root" -maxdepth 2 \( -name "UnitTests.xml" -o -name "phpunit.xml" -o -name "phpunit.xml.dist" \) 2>/dev/null | head -1
```

- No report → suggest `test-audit-text` first, stop.
- Multiple reports → ask user (default: last alphabetically).

| Condition | Rule |
|-----------|------|
| PHPUnit ≥ 10 | `#[Test]` attribute |
| PHPUnit < 10 | `/** @test */` docblock |
| testing-framework present | `use TYPO3\TestingFramework\Core\Unit\UnitTestCase;` |
| testing-framework absent | `use PHPUnit\Framework\TestCase;` |

---

## Step 2 – Determine group

Read the second argument (case-insensitive): `Z1` → process Z1 only · `Z2` → process Z2 only · omitted or anything else → **Both** (Z1 + Z2). Never ask the user.

---

## Step 3 – Parse report and check files

TXT sections: `[Unit Z1]`, `[Unit Z2]`, `[Edge Z1]`, `[Edge Z2]`. Include sections matching `selected_group`. Deduplicate → `target_files[]`.

**Every entry in `target_files[]` must be processed — none may be skipped or omitted.**

Pre-check existence in one bash block:

```bash
for f in "${target_files[@]}"; do
  out="$ext_root/Tests/Unit/${f#Classes/}"; out="${out%.php}Test.php"
  test -f "$out" && echo "EXISTS:$out" || echo "MISSING:$out"
done
```

---

## Step 4 – Process each class

`Classes/Utility/StringHelper.php` → `Tests/Unit/Utility/StringHelperTest.php`

### EXISTS → Extend only if hints are present
1. Read test file; search for hint comments only: `// TODO`, `markTestIncomplete`, `markTestSkipped`, empty `// Arrange`, empty `// Act`, empty `// Assert`, empty method body `{}`.
2. **If no hints found → do nothing.** Status: `⏭️ skipped (no hints)`. Do not analyse coverage, do not add boundary cases, do not modify the file.
3. **If hints found →** read source class; for each hint, write the missing test case body (AAA pattern). Do not touch any other part of the file. Status: `✏️ extended (N completed)`.

### MISSING → Create
1. Read source: namespace, class name, constructor, all `public function` (skip trivial getters/setters).
2. **Z2 only** — mock dependencies:

| Signal | Strategy |
|--------|----------|
| Constructor-injected typed param | `createMock(Type::class)` as property, injected in `setUp()` |
| `renderingContext->getVariableProvider()` | Stub both; see ViewHelper pattern below |
| `GeneralUtility::makeInstance(X::class)` | `GeneralUtility::addInstance(X::class, $mock)` in `setUp()` |

3. Plan **2–3 tests per public method**: happy path · boundary (null/empty/zero/max) · alternative branch.
4. `mkdir -p` if needed. Note any `.php_`/`.php.bak`/`.php.disabled` variants in summary.
5. Write. Status: `✅ created`.

---

## Step 5 – File content

### Namespace
`Vendor\Extension\Utility` → test namespace `Vendor\Extension\Tests\Unit\Utility`

### Z1 skeleton

```php
<?php

declare(strict_types=1);

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

### Z2 additions
- Add `use PHPUnit\Framework\MockObject\MockObject;`
- Per dependency: `private DependencyType&MockObject $mockDep;` + `$this->mockDep = $this->createMock(DependencyType::class);` in `setUp()`.

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
// Tests: $this->subject->setArguments([...]); $result = $this->subject->render();
```

**GeneralUtility pattern:**

```php
protected function setUp(): void { ...; GeneralUtility::addInstance(SomeService::class, $this->mockService); }
protected function tearDown(): void { GeneralUtility::purgeInstances(); parent::tearDown(); }
```

### Helper method signatures

When generating a `createSubject()` factory helper, always add a PHPDoc `@param` with the value type and keep the native hint as `array`:

```php
/** @param array<string, mixed> $arguments */
private function createSubject(array $arguments): {ClassName}
```

`array<string, mixed>` is not valid PHP syntax in a native type hint — it belongs only in the PHPDoc. PHPStan (rule `missingType.iterableValue`) reads the PHPDoc and accepts this pattern. This applies regardless of whether the parameter has a default value (`array $arguments = []`) or additional parameters (`array $arguments, \Closure $fn = null`).

---

### Test method format

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

Naming: `returnsEmptyStringForEmptyInput`, `throwsExceptionForNegativeValue` — never `test1`, `works`.

Assertions: `assertSame` (scalar) · `assertStringContainsString` (substring) · `assertTrue/False` · `assertNull` · `expectException` · `assertCount` · `assertInstanceOf` · `expects()->method()->with()` (verified call).

---

## Step 6 – Summary

**Always output in English.**

```
## Generated Unit Tests — Group {selected_group}
| Source class | Test file | Status | Notes |
|---|---|---|---|
| ... | ... | ✅ created / ✏️ extended (N) / ⏭️ skipped (no hints) | |

PHP: {version} · PHPUnit: {major}.x · TYPO3: {version} · testing-framework: {version}
```

---

## Quality rules

- `declare(strict_types=1)` · `final class` · all test methods `public function (): void`
- Arrange/Act/Assert comments mandatory (omit Arrange only if empty)
- `parent::setUp()` first in `setUp()`; `parent::tearDown()` last in `tearDown()`
- No `@covers`, no magic numbers, no `ConnectionPool`/`QueryBuilder` in unit tests
- **Z1:** No mocks — flag as "Z2 misclassified" and skip if a stub is needed
- **"Does not throw" tests:** Never use `assertTrue(true)` as a no-op assertion. Use `$this->expectNotToPerformAssertions()` at the top of the method (before Arrange) and omit all Arrange/Act/Assert comments — PHPUnit then counts the test as intentionally assertion-free.
- **Tautological assertions:** Never assert on a value whose type PHPStan already knows statically. `assertInstanceOf(Foo::class, $x)` when `$x: Foo` is always true — assert on the actual value instead (e.g. `assertSame($expected, $x->getValue())`). `assertIsString($s)` when `$s: string` is always true — use `assertStringContainsString(...)`, `assertSame(...)`, or `assertNotEmpty(...)`; if a stronger assertion already follows on the same variable, remove `assertIsString` entirely. `assertTrue(true)` is never a valid assertion.
- **TYPO3 `DebuggerUtility::var_dump()`:** Returns `''` when `inline=false` (output goes to page buffer). Tests asserting on the returned string **must** pass `inline: true`. Tests only checking `assertIsString()` may use `inline: false`.
- **Z2:** Final classes → `new`, never `createMock()`/`createStub()` on final; `willReturn()` must match declared return type; never `->with()` on a stub without `expects()` (PHPUnit 14 deprecation); use `&MockObject` if any test calls `expects()`, else `&Stub`; when the property is `&MockObject` but a specific test does not call `expects()`, add `#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]` on that test method; `GeneralUtility::purgeInstances()` in `tearDown()` when `addInstance()` used; never mock the class under test
