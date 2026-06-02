---
name: test-audit-text
description: Analyzes all PHP classes in a TYPO3 extension and produces a balance report of how many classes exist, how many are suitable for PHPUnit Unit Tests vs. Functional Tests, with a short justification per class. Use this skill when the user asks about "test audit", "test coverage overview", "which classes should I test", or "unit vs functional" for a TYPO3 extension.
argument-hint: <extension-path-or-name>
---

# TYPO3 Test Audit

## Step 1 – Resolve extension root

Resolve `ext_root` (contains `Classes/`, `composer.json`) and `classes_dir` = `$ext_root/Classes`.

Search order: absolute path → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`.

If no argument, ask the user.

## Step 2+3 – Discover and analyse via batch grep

Run all four commands; do **not** read individual files.

```bash
# 1. All PHP files (sorted)
find "$classes_dir" -name "*.php" | sort

# 2. Class declarations + inheritance
grep -rn "^abstract class \|^class \|^interface \|^trait \|extends \|implements " \
     "$classes_dir" --include="*.php"

# 3. TYPO3 integration signals
grep -rn "ConnectionPool\|QueryBuilder\|makeInstance\|GlobalsService\|GLOBALS\[.TSFE\|BackendUser\|AbstractViewHelper\|AbstractConditionViewHelper\|CacheManager\|PersistenceManager\|RepositoryInterface" \
     "$classes_dir" --include="*.php"

# 4. Constructor / injection (for Z1/Z2)
grep -rn "public function __construct\|#\[Inject\]\|@inject\|LocalizationUtility\|Environment::" \
     "$classes_dir" --include="*.php"
```

Map each file's signals from these four outputs — **do not** open individual files.

## Step 4 – Classification

### Not directly testable
Interface, Trait, Abstract class → skip.

---

### Unit Test — qualify when ALL true
- No `AbstractViewHelper` / `AbstractConditionViewHelper` parent
- No `ConnectionPool` / `QueryBuilder`
- No `makeInstance` for repository / service / TYPO3 subsystem (plain PHP helpers: OK)
- No `GlobalsService`, `$GLOBALS['TSFE']`, `BackendUser`
- All dependencies plain PHP or constructor-injectable

**Also qualify:** Extbase Validators, PSR-14 Events/Listeners (pure data), TypeConverters (no DB), TCA hooks, `Environment::*`-only statics, `GeneralUtility` string/array helpers, `LocalizationUtility::translate()` (static, mockable).

**Exclude (glue code):** Extbase controller actions, model getters/setters only, framework wiring.

---

### Functional Test — any one signal present
`AbstractViewHelper` / `AbstractConditionViewHelper` parent · `ConnectionPool` / `QueryBuilder` · `makeInstance` for repository/persistence/TYPO3 subsystem · `GlobalsService` / `BackendUser` / `$GLOBALS['TSFE']` · TYPO3 caching infrastructure · Extbase Persistence/Repository · Extbase controller.

---

### Edge case — both viable
ViewHelper with non-trivial render logic (sorting, formatting, branching) · Service/Utility with `LocalizationUtility` but significant own logic · `makeInstance` for plain PHP helper only.

---

### Z1 / Z2 sub-classification (Unit and Edge)

**Z1** — no constructor params (or primitives only), no `makeInstance`, no injected interface/abstract.

**Z2** — any: constructor-injected interface/abstract · needs mock interaction verification · `makeInstance` for mockable target.

> **CRITICAL:** Every Unit and Edge class MUST have a Z1/Z2 assignment.

---

## Step 5 – Output the balance report

Do **not** scan `Tests/` directories. No coverage information.

---

# Test Audit: `<extension-name>`

*Generated: YYYY-MM-DD · HH:MM:SS*

## Summary

| Category                          | Count |
|----------------------------------|-------|
| PHP files total                  | N     |
| Suitable for Unit Tests          | N     |
| — Z1 (no stub/mock)              | N     |
| — Z2 (stub/mock needed)          | N     |
| Edge case (both possible)        | N     |
| — Z1 (no stub/mock)              | N     |
| — Z2 (stub/mock needed)          | N     |
| Suitable for Functional Tests    | N     |
| Not directly testable            | N     |

---

## Suitable for Unit Tests (N classes)

#### Z1 — No stub/mock needed (N)

**`ClassName`** ([Classes/Path/To/Class.php](Classes/Path/To/Class.php))
> Reason why Z1.

#### Z2 — Stub or mock required (N)

**`ClassName`** ([Classes/Path/To/Class.php](Classes/Path/To/Class.php))
> Reason why Z2.

---

## Edge case – both Unit and Functional viable (N classes)

#### Z1 — No stub/mock needed (N)

**`ClassName`** ([Classes/Path/To/Class.php](Classes/Path/To/Class.php))
> Reason why Z1 and why Edge case.

#### Z2 — Stub or mock required (N)

**`ClassName`** ([Classes/Path/To/Class.php](Classes/Path/To/Class.php))
> Reason why Z2 and why Edge case.

---

## Suitable for Functional Tests (N classes)

**`ClassName`** ([Classes/Path/To/Class.php](Classes/Path/To/Class.php))
> Reason.

---

## Not directly testable (N classes)

**`ClassName`** ([Classes/Path/To/Class.php](Classes/Path/To/Class.php))
> Reason.

---

## Priority recommendation

1. Z1/Z2 Unit classes with complex data-munging logic → highest ROI
2. Edge cases → unit test the algorithm first, functional test the integration
3. Functional: ViewHelpers/repositories touching DB → end-to-end scenarios
4. De-prioritize: glue code, controller pass-throughs, model getters/setters

---

## Step 6 – Save output files

```bash
date +"%Y-%m-%d %H:%M:%S"
```

### 6a – Write MD

`$ext_root/test-audit-<extension-name>.md` — overwrite silently.

```markdown
---
title: "Test Audit – <extension-name>"
date: <YYYY-MM-DD>
time: "<HH:MM:SS>"
extension: <extension-name>
classes_total: <N>
unit: <N>
unit_z1: <N>
unit_z2: <N>
edge: <N>
edge_z1: <N>
edge_z2: <N>
functional: <N>
not_testable: <N>
---

(full Step 5 report)
```

### 6b – Write TXT

`$ext_root/test-audit-<extension-name>.txt` — overwrite silently.

```
# Test Audit – <extension-name>
# Generated: <YYYY-MM-DD> <HH:MM:SS>

[Unit Z1 – N]
Classes/Path/To/ClassName.php
...

[Unit Z2 – N]
Classes/Path/To/ClassName.php
...

[Edge Z1 – N]
Classes/Path/To/ClassName.php
...

[Edge Z2 – N]
Classes/Path/To/ClassName.php
...
```

Paths relative to `ext_root`, sorted alphabetically per group, omit empty groups.

### 6c – Confirm

```
✔ MD   → <absolute path>
✔ TXT  → <absolute path>
```
