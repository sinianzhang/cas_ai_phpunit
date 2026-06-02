---
name: generate-unit-tests
description: Reads the test-audit-*.txt report for a TYPO3 extension, asks the user whether to generate Z1, Z2, or both, then processes all classes of the chosen group across both Unit and Edge sections. If a test file already exists it analyses its comments and fills in missing test cases (AAA pattern). If no test file exists it generates a new test class with 2–3 test cases per public method. Use when the user asks to "generate unit tests", "write unit tests for Z1", "create tests for Z2 classes", "generate PHPUnit tests from audit report".
argument-hint: <extension-path-or-name>
---

# Generate PHPUnit Unit Tests for Z1 / Z2 Classes

Reads `test-audit-*.txt`, asks which group (Z1 / Z2 / both), then for each class either extends an existing test file or creates a new one.

---

## Step 1 – Resolve extension root and locate TXT report

**Resolve `ext_root`** (directory containing `Classes/`, `Tests/`, `composer.json`):

1. Absolute path → use directly
2. `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`

If no argument, ask the user. Also store `project_root` (directory containing `vendor/`).

**Locate the TXT report:**

```bash
find "$ext_root" -maxdepth 1 -name "test-audit-*.txt" | sort
```

- **No file found** → inform the user, suggest running `test-audit-text` first, and **stop**.
- **One file** → use it, tell the user which file.
- **Multiple files** → show numbered list, ask which to use (default: last alphabetically).

---

## Step 2 – Ask the user which group to generate

Ask using `AskUserQuestion`:

- **Z1** — No stub/mock needed
- **Z2** — Stub/mock required
- **Both** — Z1 and Z2

Store as `selected_group`.

---

## Step 3 – Collect environment context

Run all in one bash block:

```bash
php --version 2>/dev/null | head -1
cat "$project_root/vendor/phpunit/phpunit/composer.json" 2>/dev/null | grep '"version"' | head -1
cat "$project_root/vendor/typo3/cms-core/composer.json" 2>/dev/null | grep '"version"' | head -1
cat "$project_root/vendor/typo3/testing-framework/composer.json" 2>/dev/null | grep '"version"' | head -1
find "$ext_root/Build/phpunit" "$ext_root" -maxdepth 2 \( -name "UnitTests.xml" -o -name "phpunit.xml" -o -name "phpunit.xml.dist" \) 2>/dev/null | head -1
```

Extract and store `php_version`, `phpunit_major`, `typo3_version`, `framework_version`. Read the phpunit config file if found; note bootstrap constants.

| Condition | Rule |
|-----------|------|
| PHPUnit ≥ 10 | `#[Test]` attribute style |
| PHPUnit < 10 | `/** @test */` docblock style |
| testing-framework present | `use TYPO3\TestingFramework\Core\Unit\UnitTestCase;` |
| testing-framework absent | `use PHPUnit\Framework\TestCase;` |

---

## Step 4 – Parse TXT report and build class list

TXT format:

```
[Unit Z1 – N]
Classes/Path/To/ClassName.php
...

[Unit Z2 – N]
...

[Edge Z1 – N]
...

[Edge Z2 – N]
...
```

| `selected_group` | Sections to include |
|------------------|---------------------|
| Z1 | `[Unit Z1]` and `[Edge Z1]` |
| Z2 | `[Unit Z2]` and `[Edge Z2]` |
| Both | all four sections |

Store deduplicated paths as `target_files[]`. If empty, inform user and stop.

**Pre-batch existence checks** for all classes at once:

```bash
for f in "${target_files[@]}"; do
  out="$ext_root/Tests/Unit/${f#Classes/}"; out="${out%.php}Test.php"
  test -f "$out" && echo "EXISTS:$out" || echo "MISSING:$out"
done
```

Store results as a lookup map before entering the per-class loop.

---

## Step 5 – Process each class

For each path in `target_files[]`, derive the test file path:

```
Classes/Utility/StringHelper.php → Tests/Unit/Utility/StringHelperTest.php
```

Use the pre-batched existence result to branch:

---

### Branch A — Test file EXISTS → extend

1. Read the existing test file.
2. Collect **incomplete methods**: bodies with `// TODO`, `// @todo`, `// FIXME`, empty `// Arrange/Act/Assert` blocks, `markTestIncomplete(...)`, `markTestSkipped(...)`, or empty `{}`.
3. Read the source class; match each incomplete test method back to its source method.
4. Replace placeholder bodies with proper AAA implementations (see Step 6 for format).
5. Append any obviously missing test cases (boundary/alternative) for under-tested methods.
6. Write updated file. Status: `✏️ extended (N incomplete tests completed)`.

---

### Branch B — Test file MISSING → create

1. Read source class. Extract:
   - `namespace`, `class_name`, `constructor` signature
   - All `public function` declarations (skip trivial getters/setters): name, params, return type, body

2. **Z2 only** — identify mock dependencies:

   | Signal in source | Mock strategy |
   |------------------|---------------|
   | Constructor-injected typed param | `createMock(Type::class)` as property, injected in `setUp()` |
   | `renderingContext->getVariableProvider()` | Stub `renderingContext`; return stub `variableProvider` |
   | `GeneralUtility::makeInstance(X::class)` | `GeneralUtility::addInstance(X::class, $mock)` in `setUp()` |
   | Setter / `initialize*()` | Call setter in `setUp()` after construction |

3. Plan **2–3 test cases per public method**:
   - Case 1: happy path (typical valid input → expected output)
   - Case 2: boundary (empty, zero, null where nullable, max value)
   - Case 3: alternative branch (different input exercising a different code path, if one exists)

4. `mkdir -p "$(dirname "$output_path")"` if needed.

5. Check for disabled variants (`.php_`, `.php.bak`, `.php.disabled`, `.php.skip`) — note in summary if found, do not modify them.

6. Write new test file. Status: `✅ created`.

---

## Step 6 – Test file content

### Namespace

Replace `Classes\` with `Tests\Unit\` in the source namespace:

```
Source:  Vendor\Extension\Utility
Test:    Vendor\Extension\Tests\Unit\Utility
```

### Z1 skeleton

```php
<?php

declare(strict_types=1);

namespace {test_namespace};

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use {source_namespace}\{ClassName};
{additional_use_statements}

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

### Z2 additions to the Z1 skeleton

- Add `use PHPUnit\Framework\MockObject\MockObject;`
- Add one typed property per dependency: `private DependencyType&MockObject $mockDep;`
- In `setUp()` before constructing subject: `$this->mockDep = $this->createMock(DependencyType::class);`
- Pass mocks to constructor: `$this->subject = new ClassName($this->mockDep, ...);`

**ViewHelper special pattern:**

```php
use PHPUnit\Framework\MockObject\Stub;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

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
// In test methods: $this->subject->setArguments([...]); $result = $this->subject->render();
```

**GeneralUtility::addInstance pattern:**

```php
protected function setUp(): void
{
    parent::setUp();
    $this->mockService = $this->createMock(SomeService::class);
    GeneralUtility::addInstance(SomeService::class, $this->mockService);
    $this->subject = new {ClassName}();
}

protected function tearDown(): void
{
    GeneralUtility::purgeInstances();
    parent::tearDown();
}
```

### Test method format

```php
#[Test]
public function {methodName}{Scenario}(): void
{
    // Arrange
    $input = ...;
    $expected = ...;

    // Act
    $result = $this->subject->{methodName}($input);

    // Assert
    self::assertSame($expected, $result);
}
```

*For PHPUnit < 10: replace `#[Test]` with a `/** @test */` docblock.*

**Naming:** camelCase sentences — `returnsEmptyStringForEmptyInput`, `throwsExceptionForNegativeValue`. Never `test1`, `works`.

**Assertions:**

| Situation | Assertion |
|-----------|-----------|
| Exact scalar | `assertSame($expected, $result)` |
| String contains | `assertStringContainsString($needle, $result)` |
| Boolean | `assertTrue` / `assertFalse` |
| Null | `assertNull($result)` |
| Exception | `$this->expectException(X::class)` before Act |
| Count | `assertCount($n, $result)` |
| Type | `assertInstanceOf(X::class, $result)` |
| Call verified | `$this->mockX->expects($this->once())->method('foo')->with($arg)` |

---

## Step 7 – Summary

**Always output in English, regardless of conversation language.**

```
## Generated Unit Tests — Group {selected_group}

| Source class | Test file | Status | Notes |
|---|---|---|---|
| Classes/Utility/StringHelper.php | Tests/Unit/Utility/StringHelperTest.php | ✅ created | |
| Classes/Service/PriceService.php | Tests/Unit/Service/PriceServiceTest.php | ✏️ extended (3 incomplete tests completed) | |
| Classes/Domain/Model/Value/Money.php | Tests/Unit/Domain/Model/Value/MoneyTest.php | ✅ created | 💤 disabled variant: MoneyTest.php_ |

Environment used:
  PHP               : {php_version}
  PHPUnit           : {phpunit_major}.x  (attribute style: yes/no)
  TYPO3             : {typo3_version}
  Test base         : {UnitTestCase FQCN}
  testing-framework : {framework_version}
```

---

## Quality rules (MUST follow)

**All classes:**
- No `ConnectionPool` / `QueryBuilder` imports in unit tests
- No `@covers` annotations
- No magic numbers — use named `$expected` variables
- All test methods: `public function ... (): void`
- All test classes: `final class`
- `declare(strict_types=1)` as second line (after `<?php`)
- Arrange / Act / Assert comments mandatory (omit Arrange only if empty)
- `parent::setUp()` as first line of `setUp()`; `parent::tearDown()` as last line of `tearDown()`

**Z1:** No mocks. If a stub is needed, flag as "Z2 misclassified" and skip.

**Z2:**
- Final classes: instantiate directly with `new ClassName(...)` — never `createMock()`/`createStub()` on a final class
- `willReturn()` must satisfy the method's declared return type — use `createStub(ReturnType::class)`, never `new \stdClass()`
- If any test calls `expects()` on a dependency → declare it as `&MockObject`; add `#[AllowMockObjectsWithoutExpectations]` on test methods that do not call `expects()`
- If no test calls `expects()` → declare as `&Stub`
- Never `->with(…)` on a stub without `expects()` — PHPUnit 14 deprecation; use pure `->method()->willReturn()` or chain `expects()->method()->with()`
- `GeneralUtility::purgeInstances()` in `tearDown()` whenever `addInstance()` is used
- Do not mock the class under test
