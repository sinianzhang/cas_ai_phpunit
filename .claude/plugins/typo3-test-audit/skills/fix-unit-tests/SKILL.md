---
name: fix-unit-tests
description: Reads the infection-report.txt for a TYPO3 extension and adds targeted PHPUnit test cases for every escaped mutant found in it (classes listed under [Not testable] in the audit file are skipped if the audit file is present). Use when the user asks to "fix unit tests", "kill escaped mutants", "fix mutation test", or "improve tests from infection report".
argument-hint: <extension-path-or-name>
---

# Fix Unit Tests from Infection Report

> **No corrections, no guessing.** Audit file and infection report are authoritative. Never modify them. Never rename or rewrite source/test files to resolve mismatches — report and skip.

---

## Step 1 – Resolve paths & detect environment

Resolve `ext_root` (dir with `Classes/`, `composer.json`): absolute → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`. No argument → ask.

```bash
ext_root="<resolved>" extension_name=$(basename "$ext_root")
project_root="$(git -C "$ext_root" rev-parse --show-toplevel 2>/dev/null || echo "$ext_root")"
audit_file="$ext_root/test-audit-${extension_name}.txt"
report="$ext_root/Build/infection/infection-report.txt"
{ test -f "$report"     && echo "FOUND:$report"; }      || echo "MISSING:$report"
{ test -f "$audit_file" && echo "FOUND:$audit_file"; }  || echo "WARNING:$audit_file not found — [Not testable] filter skipped"
ddev exec php vendor/bin/phpunit --version 2>/dev/null | head -1
find "$ext_root/Build/phpunit" "$ext_root" -maxdepth 2 \
  \( -name "UnitTests.xml" -o -name "phpunit.xml" -o -name "phpunit.xml.dist" \) 2>/dev/null | head -1
```

**`$report` missing → print and stop. `$audit_file` missing → warn and continue without [Not testable] filter.**

| Condition | Rule |
|---|---|
| PHPUnit ≥ 10 | `#[Test]` attribute |
| PHPUnit < 10 | `/** @test */` docblock |
| testing-framework present | extend `TYPO3\TestingFramework\Core\Unit\UnitTestCase` |
| testing-framework absent | extend `PHPUnit\Framework\TestCase` |

---

## Step 2 – Extract escaped mutants from infection report

**2a.** From `$report`, read only between `Escaped mutants:` and the next section header or EOF. Per entry extract: `source_file` (strip `/var/www/html/` then `<ext_root_rel>/`, e.g. `Classes/ViewHelpers/ForViewHelper.php`), `line_number`, `mutation_type`, diff block. Keep all entries.

**2b.** If `$audit_file` is present: collect file paths under the `[Not testable – N]` section header (until next `[…]` or EOF) → `not_testable_classes[]`. Discard any entry from 2a whose `source_file` exactly matches `not_testable_classes[]`.

**0 survivors → print "No escaped mutants found — nothing to fix." and stop.**

Group survivors by `source_file`.

---

## Step 3 – Test strategy per mutation type

| Mutation type | Test requirement |
|---|---|
| `LessThanOrEqualTo` / `LessThan` | Assert at exact boundary value |
| `GreaterThanOrEqualTo` / `GreaterThan` | Assert at exact upper boundary |
| `Plus` / `Minus` | Assert exact computed value |
| `TrueValue` / `FalseValue` | Test both `true` and `false` paths |
| `Concat` | Assert all concatenated parts present in output |
| `Return_` / `Null` | Assert correct return for that branch |
| `DecrementInteger` / `IncrementInteger` | Assert the exact integer value (e.g. default, threshold) |
| `MethodCallRemoval` on `registerArgument()` | Call `$subject->prepareArguments()` and assert the key exists with the correct `getDefaultValue()` / `isRequired()` — see note below |
| `Assignment` / `PlusEqual` | Assert the final state or return value after the full operation |

> **`MethodCallRemoval` on `initializeArguments()` / `registerArgument()` calls**: these cannot be killed by `render()` tests that explicitly set all arguments via `setArguments()`. Instead, call `prepareArguments()` on a bare `new ClassName()` and assert the argument definition: `assertArrayHasKey`, `isRequired()`, `getDefaultValue()`. (TYPO3Fluid: `prepareArguments()` is public and returns `ArgumentDefinition[]`.)

---

## Step 4 – Write or extend test files

**4a.** Map source → test path, check existence:
```bash
test_file="$ext_root/Tests/Unit/${relative_class_path#Classes/}"
test_file="${test_file%.php}Test.php"
test -f "$test_file" && echo "EXISTS:$test_file" || echo "MISSING:$test_file"
```

**4b. Read context** — Read each mutated method and its neighbours for signatures/return values. Read the existing test file for the current skeleton and methods.

**4b-check. Skip already-covered mutants** — If an existing test already exercises the exact boundary or branch this mutant targets (and asserts the expected outcome), mark it `⏭️ already covered` and skip.

**4c. MISSING → create skeleton:**
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

**4d. EXISTS → append only** — Insert new methods before the closing `}`. Never touch existing code.

**4e. One method per escaped mutant:**
```php
#[Test]
public function {descriptiveCamelCaseName}(): void
{
    // Arrange
    $input = ...; $expected = '...';
    // Act
    $result = $this->subject->{method}($input);
    // Assert
    self::assertSame($expected, $result);
}
```
Names describe the assertion in plain English (e.g. `greetWithTimeReturnsMorningAtNoon`). Omit `// Arrange` if empty.

---

## Step 5 – Run & fix once

```bash
ddev exec php vendor/bin/phpunit -c {phpunit_xml} {affected_test_files}
```

On failure: read the output, fix the method, re-run once. If still failing, report the error and stop.

---

## Step 6 – Summary (always in English)

```
## Fix Unit Tests — Results
| Source file | Mutants targeted | Test file | Status |
|---|---|---|---|
| Greeter.php | 2 (LessThanOrEqualTo ×2) | Tests/Unit/Dummy/GreeterTest.php | ✅ tests pass |
```

---

## Quality rules

1. `assertSame` for scalars · `assertStringContainsString` for partials · `expectException` for exceptions
2. No `@covers`, no magic numbers, no `ConnectionPool`/`QueryBuilder` in unit tests
3. No tautological assertions (never assert a statically-known type)
4. Never mock the class under test
5. `parent::tearDown()` last if `tearDown` is added
