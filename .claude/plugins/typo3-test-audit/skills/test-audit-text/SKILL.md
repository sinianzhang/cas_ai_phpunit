---
name: test-audit-text
description: Analyzes all PHP classes in a TYPO3 extension and produces a balance report of how many classes exist, how many are suitable for PHPUnit Unit Tests vs. Functional Tests, with a short justification per class. Use this skill when the user asks about "test audit", "test coverage overview", "which classes should I test", or "unit vs functional" for a TYPO3 extension.
argument-hint: <extension-path-or-name>
---

# TYPO3 Test Audit

## Step 1 – Resolve extension root

Resolve `ext_root` (contains `Classes/`, `composer.json`) and `classes_dir` = `$ext_root/Classes`. Search order: absolute → `packages/<arg>` → `packages-dev/<arg>` → `vendor/<vendor>/<arg>`. No argument → ask.

---

## Step 2 – Batch grep (do NOT open individual files)

```bash
find "$classes_dir" -name "*.php" | sort

grep -rEn \
  "^abstract class |^class |^interface |^trait |extends |implements |\
ConnectionPool|QueryBuilder|makeInstance|GlobalsService|GLOBALS\[.TSFE|\
BackendUser|AbstractViewHelper|AbstractConditionViewHelper|AbstractTagBasedViewHelper|\
AbstractFormFieldViewHelper|CacheManager|PersistenceManager|RepositoryInterface|\
public function __construct|#\[Inject\]|@inject|LocalizationUtility|Environment::|\
renderingContext->getAttribute|public function inject[A-Z]|[^a-z]exit;|childViewHelperNodes" \
  "$classes_dir" --include="*.php"
```

Map each file's signals from these outputs — do not open individual files.

---

## Step 3 – Classification

**Skip (not directly testable):** Interface · Trait · Abstract class

| Signal present | → Category |
|---|---|
| `AbstractViewHelper` / `AbstractConditionViewHelper` / `AbstractTagBasedViewHelper` / `AbstractFormFieldViewHelper` | Functional (or Edge if non-trivial render logic) |
| `ConnectionPool` / `QueryBuilder` | Functional |
| `makeInstance` for repository/persistence/TYPO3 subsystem | Functional |
| `GlobalsService` / `BackendUser` / `$GLOBALS['TSFE']` | Functional |
| TYPO3 caching / Extbase Persistence / Extbase controller | Functional |
| None of the above | Unit |
| ViewHelper with sorting/formatting/branching · Service with `LocalizationUtility` but own logic · `makeInstance` for plain PHP helper only | Edge (both viable) |

**Also qualify as Unit:** Extbase Validators · PSR-14 Events/Listeners · TypeConverters (no DB) · TCA hooks · `Environment::*`-only · `GeneralUtility` string/array helpers · `LocalizationUtility::translate()`.

**Trait propagation:** If a class `use`s a trait that contains `makeInstance` for a TYPO3 subsystem (e.g. `PagesCacheTagService`, `CacheManager`), treat the **using class** as Functional — it inherits the subsystem dependency even though the signal appears only in the trait file.

**Exclude from Unit (glue code):** Extbase controller actions · model getters/setters only · framework wiring.

---

## Step 4 – Output the balance report

Do **not** scan `Tests/`. No coverage information.

```
# Test Audit: <extension-name>
# Generated: YYYY-MM-DD · HH:MM:SS

## Summary
| Category                       | Count |
|-------------------------------|-------|
| PHP files total               | N     |
| Suitable for Unit Tests       | N     |
| Edge case (both possible)     | N     |
| Suitable for Functional Tests | N     |
| Not directly testable         | N     |

## Suitable for Unit Tests (N)
**`ClassName`** (Classes/Path/ClassName.php) — reason

## Edge case (N)
**`ClassName`** (Classes/Path/ClassName.php) — reason

## Suitable for Functional Tests (N)
**`ClassName`** (Classes/Path/ClassName.php) — reason

## Not directly testable (N)
**`ClassName`** (Classes/Path/ClassName.php) — reason

## Priority recommendation
1. Unit classes with complex logic → highest ROI
2. Edge cases → unit-test algorithm first, functional-test integration
3. Functional: ViewHelpers/repositories touching DB
4. De-prioritize: glue code, controller pass-throughs, model getters/setters
```

---

## Step 5 – Save output files

```bash
date +"%Y-%m-%d %H:%M:%S"
```

**MD** → `$ext_root/test-audit-<extension-name>.md` (overwrite silently):
```
---
title: "Test Audit – <extension-name>"
date: <YYYY-MM-DD>
time: "<HH:MM:SS>"
extension: <extension-name>
classes_total: <N>
unit: <N>
edge: <N>
functional: <N>
not_testable: <N>
---
(full Step 4 report)
```

**TXT** → `$ext_root/test-audit-<extension-name>.txt` (overwrite silently):
```
# Test Audit – <extension-name>
# Generated: <YYYY-MM-DD> <HH:MM:SS>

[Unit – N]
Classes/Path/ClassName.php
...

[Edge – N]
Classes/Path/ClassName.php
...

[Not testable – N]
Classes/Path/ClassName.php
...
```
Paths relative to `ext_root`, sorted alphabetically per group, omit empty groups.

**Confirm:**
```
✔ MD  → <absolute path>
✔ TXT → <absolute path>
```
