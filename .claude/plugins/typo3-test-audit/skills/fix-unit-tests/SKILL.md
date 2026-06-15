---
name: fix-unit-tests
description: Reads the test-audit-<ext>.txt and infection-report.txt for a TYPO3 extension, then adds targeted PHPUnit test cases for every escaped mutant that belongs to a class listed under [Unit] in the audit file. Use when the user asks to "fix unit tests", "kill escaped mutants", "fix mutation test", or "improve tests from infection report".
argument-hint: <extension-path-or-name>
---

# Fix Unit Tests from Infection Report

> **Global principle — no corrections, no guessing:**
> The audit file and infection report are treated as authoritative. Never modify either file. Never rename, rewrite, or create source/test files to make a mismatch work. If a path in the audit file does not exist on disk, or a mutant's source file does not match the audit, report it and move on.

---

## Step 1 – Resolve paths, verify inputs & detect environment

Resolve `ext_root` (dir containing `Classes/`, `composer.json`): absolute → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`. No argument → ask. Derive `extension_name` = basename of `ext_root`. Store `project_root` (dir containing `vendor/`).

```bash
ext_root="<resolved>"
audit_file="$ext_root/test-audit-${extension_name}.txt"
report="$ext_root/Build/infection/infection-report.txt"

test -f "$audit_file" && echo "FOUND:$audit_file"  || echo "MISSING:$audit_file"
test -f "$report"     && echo "FOUND:$report"       || echo "MISSING:$report"

grep '"version"' "$project_root/vendor/phpunit/phpunit/composer.json" 2>/dev/null | head -1
find "$ext_root/Build/phpunit" "$ext_root" -maxdepth 2 \
  \( -name "UnitTests.xml" -o -name "phpunit.xml" -o -name "phpunit.xml.dist" \) 2>/dev/null | head -1
```

**If either file is missing → print an error naming the missing file and stop.**

| Condition | Rule |
|-----------|------|
| PHPUnit ≥ 10 | `#[Test]` attribute |
| PHPUnit < 10 | `/** @test */` docblock |
| testing-framework present | `use TYPO3\TestingFramework\Core\Unit\UnitTestCase;` |
| testing-framework absent | `use PHPUnit\Framework\TestCase;` |

---

## Step 2 – Extract allowed classes & filter escaped mutants

**2a.** Parse `$audit_file`. Extract all file paths under the section header matching `[Unit – N]` (em dash, any count) until the next `[…]` header or EOF. Store as `unit_classes[]` (paths relative to `ext_root`, e.g. `Classes/Dummy/Greeter.php`).

**2b.** From `$report`, read only the block between `Escaped mutants:` and the next section header (`Timed Out mutants:` / `Skipped mutants:`) or EOF. For each numbered entry parse:

| Field | Source |
|---|---|
| `source_file` | First line after entry number, before `[M]`; strip leading `/var/www/html/` to get project-relative path |
| `line_number` | `:N` suffix on same line |
| `mutation_type` | `[M] <Type>` value |
| `diff` | `@@` block lines that follow |

Keep only mutants whose `source_file` exactly matches an entry in `unit_classes[]`. Discard the rest silently.

**If 0 mutants remain → print "No escaped mutants in Unit classes — nothing to fix." and stop.**

Group surviving mutants by `source_file`.

---

## Step 3 – Test strategy per mutation type

| Mutation type | Required test |
|---|---|
| `LessThanOrEqualTo` / `LessThan` | Assert with input == boundary value |
| `GreaterThanOrEqualTo` / `GreaterThan` | Assert with input == upper boundary value |
| `Plus` / `Minus` | Assert exact computed value |
| `TrueValue` / `FalseValue` | Test both `true` and `false` paths |
| `Concat` | Assert output contains all concatenated parts |
| `Return_` / `Null` | Assert correct return value for that branch |

---

## Step 4 – Write or extend test files

**For each affected source file:**

**4a. Path mapping**

```bash
test_file="$ext_root/Tests/Unit/${relative_class_path#Classes/}"
test_file="${test_file%.php}Test.php"
test -f "$test_file" && echo "EXISTS:$test_file" || echo "MISSING:$test_file"
```

**4b. Read context** — Read the complete method(s) containing each mutant's `line_number`, plus the immediately surrounding methods, to capture the full signature, parameter names, and return values. Read the existing test file (if any) for the class skeleton and current test methods.

**4c. MISSING test file → create skeleton**

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

**4d. EXISTS test file → append only** — Insert new test methods after the last existing test method, before the closing `}`. Do not touch any existing code.

**4e. One test method per escaped mutant**

```php
#[Test]
public function {camelCaseSentence}(): void
{
    // Arrange
    $input = ...; $expected = '...';
    // Act
    $result = $this->subject->{method}($input);
    // Assert
    self::assertSame($expected, $result);
}
```

Naming: describe the assertion in plain English — e.g. `greetWithTimeOfDayReturnsMorningGreetingAtNoon`, never `test1` or `works`. Omit `// Arrange` only if the block is empty.

---

## Step 5 – Run tests & fix once

```bash
ddev exec php vendor/bin/phpunit -c {phpunit_xml} {affected_test_files}
```

If a test fails: read the failure output, fix the test method, re-run once. If it still fails after one fix attempt, report the failure with the error message and stop — do not retry further.

---

## Step 6 – Summary

**Always output in English.**

```
## Fix Unit Tests — Results
| Source file | Mutants targeted | Test file | Status |
|---|---|---|---|
| Greeter.php | 2 (LessThanOrEqualTo ×2) | Tests/Unit/Dummy/GreeterTest.php | ✅ tests pass |
```

---

## Quality rules

| # | Rule |
|---|------|
| 1 | `assertSame` for scalars · `assertStringContainsString` for partials · `expectException` for exceptions |
| 2 | No `@covers`, no magic numbers, no `ConnectionPool`/`QueryBuilder` in unit tests |
| 3 | No tautological assertions — never assert a statically-known type |
| 4 | Never mock or stub the class under test |
| 5 | `parent::tearDown()` last if `tearDown` is added |
