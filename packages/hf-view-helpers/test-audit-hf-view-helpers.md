---
title: "Test Audit – hf-view-helpers"
date: 2026-06-02
time: "22:43:46"
extension: hf-view-helpers
classes_total: 47
unit: 4
unit_z1: 3
unit_z2: 1
edge: 18
edge_z1: 17
edge_z2: 1
functional: 22
not_testable: 3
---

# Test Audit: `hf-view-helpers`

*Generated: 2026-06-02 · 22:43:46*

## Summary

| Category                          | Count |
|----------------------------------|-------|
| PHP files total                  | 47    |
| Suitable for Unit Tests          | 4     |
| — Z1 (no stub/mock)              | 3     |
| — Z2 (stub/mock needed)          | 1     |
| Edge case (both possible)        | 18    |
| — Z1 (no stub/mock)              | 17    |
| — Z2 (stub/mock needed)          | 1     |
| Suitable for Functional Tests    | 22    |
| Not directly testable            | 3     |

---

## Suitable for Unit Tests (4 classes)

#### Z1 — No stub/mock needed (3)

**`Email`** ([Classes/Dummy/Email.php](Classes/Dummy/Email.php))
> Plain PHP class with no TYPO3 integration signals, no constructor params, no inheritance — fully isolated unit.

**`ErrorHandler`** ([Classes/Dummy/ErrorHandler.php](Classes/Dummy/ErrorHandler.php))
> Implements `ErrorHandlerInterface` but carries no TYPO3 dependencies and no constructor injection — testable with pure PHP.

**`Greeter`** ([Classes/Dummy/Greeter.php](Classes/Dummy/Greeter.php))
> No inheritance, no integration signals, no constructor params — straightforward pure-PHP unit.

#### Z2 — Stub or mock required (1)

**`Service`** ([Classes/Dummy/Service.php](Classes/Dummy/Service.php))
> `final readonly class` constructor-injecting `ErrorHandler` (non-primitive) — dependency must be provided or stubbed in tests; no TYPO3 signals otherwise.

---

## Edge case – both Unit and Functional viable (18 classes)

#### Z1 — No stub/mock needed (17)

**`CalcViewHelper`** ([Classes/ViewHelpers/CalcViewHelper.php](Classes/ViewHelpers/CalcViewHelper.php))
> Extends `AbstractViewHelper` (Functional signal) but contains non-trivial arithmetic evaluation logic — the algorithm is independently testable as a unit; no constructor params, no `makeInstance`.

**`CaseViewHelper`** ([Classes/ViewHelpers/Format/CaseViewHelper.php](Classes/ViewHelpers/Format/CaseViewHelper.php))
> Extends `AbstractViewHelper`; string case-transformation with branching over mode options — logic is self-contained and unit-testable without Fluid bootstrap.

**`CleanHtmlViewHelper`** ([Classes/ViewHelpers/Format/CleanHtmlViewHelper.php](Classes/ViewHelpers/Format/CleanHtmlViewHelper.php))
> Extends `AbstractViewHelper`; HTML stripping/cleaning logic is non-trivial and free of TYPO3 subsystem calls — algorithm can be verified in unit scope.

**`ContainsViewHelper`** ([Classes/ViewHelpers/ContainsViewHelper.php](Classes/ViewHelpers/ContainsViewHelper.php))
> Extends `AbstractViewHelper`; performs contains-check with type-specific branching (string, array, ObjectStorage) — no constructor params, no TYPO3 infrastructure beyond the ViewHelper base.

**`DateViewHelper`** ([Classes/ViewHelpers/Format/DateViewHelper.php](Classes/ViewHelpers/Format/DateViewHelper.php))
> Extends `AbstractViewHelper`; date parsing and formatting with locale/format/timezone options — the formatting logic is unit-testable without TYPO3 bootstrap.

**`DurationViewHelper`** ([Classes/ViewHelpers/Debug/DurationViewHelper.php](Classes/ViewHelpers/Debug/DurationViewHelper.php))
> Extends `AbstractViewHelper`; duration calculation and display logic — no injected dependencies, algorithm is isolated.

**`ExistsViewHelper`** ([Classes/ViewHelpers/File/ExistsViewHelper.php](Classes/ViewHelpers/File/ExistsViewHelper.php))
> Extends `AbstractConditionViewHelper`; condition logic uses `Environment::getPublicPath()` (static, mockable) with `file_exists`/`is_dir` — the resolution path algorithm is unit-testable; functional test needed for real-filesystem integration.

**`ExplodeViewHelper`** ([Classes/ViewHelpers/String/ExplodeViewHelper.php](Classes/ViewHelpers/String/ExplodeViewHelper.php))
> Extends `AbstractViewHelper`; wraps `explode()` with delimiter/limit/trim options — pure string logic, no injected dependencies.

**`FileViewHelper`** ([Classes/ViewHelpers/Uri/FileViewHelper.php](Classes/ViewHelpers/Uri/FileViewHelper.php))
> Extends `AbstractViewHelper`; URI generation for file resources — no `makeInstance`, no DB signals; path-construction logic is unit-testable.

**`ForViewHelper`** ([Classes/ViewHelpers/ForViewHelper.php](Classes/ViewHelpers/ForViewHelper.php))
> Extends `AbstractViewHelper`; enhanced iteration with chunking/grouping/reverse options — the iterator logic is non-trivial and independently testable.

**`GetViewHelper`** ([Classes/ViewHelpers/GetViewHelper.php](Classes/ViewHelpers/GetViewHelper.php))
> Extends `AbstractViewHelper`; array/object property access with dot-path notation — the path-resolution algorithm is self-contained, no TYPO3 subsystem calls.

**`JsonDecodeViewHelper`** ([Classes/ViewHelpers/Format/JsonDecodeViewHelper.php](Classes/ViewHelpers/Format/JsonDecodeViewHelper.php))
> Extends `AbstractViewHelper`; wraps `json_decode` with option flags and error handling — logic is pure PHP, fully unit-testable.

**`JsonEncodeViewHelper`** ([Classes/ViewHelpers/Format/JsonEncodeViewHelper.php](Classes/ViewHelpers/Format/JsonEncodeViewHelper.php))
> Extends `AbstractViewHelper`; wraps `json_encode` with pretty-print/unicode/escape options — pure PHP logic, no injected dependencies.

**`NumberViewHelper`** ([Classes/ViewHelpers/Format/NumberViewHelper.php](Classes/ViewHelpers/Format/NumberViewHelper.php))
> Extends `AbstractViewHelper`; `number_format` with decimals/separators — pure PHP arithmetic formatting, no constructor params.

**`RoundViewHelper`** ([Classes/ViewHelpers/Format/RoundViewHelper.php](Classes/ViewHelpers/Format/RoundViewHelper.php))
> Extends `AbstractViewHelper`; rounding with precision and mode options (PHP_ROUND_HALF_*) — self-contained math logic.

**`SortObjectStorageViewHelper`** ([Classes/ViewHelpers/SortObjectStorageViewHelper.php](Classes/ViewHelpers/SortObjectStorageViewHelper.php))
> Extends `AbstractViewHelper`; sorts an Extbase `ObjectStorage` by property — the sorting algorithm is non-trivial and unit-testable without DB.

**`WhitespaceViewHelper`** ([Classes/ViewHelpers/Format/WhitespaceViewHelper.php](Classes/ViewHelpers/Format/WhitespaceViewHelper.php))
> Extends `AbstractViewHelper`; whitespace normalisation/collapsing logic — pure string manipulation, no TYPO3 subsystem dependencies.

#### Z2 — Stub or mock required (1)

**`PaginateAlphabeticalViewHelper`** ([Classes/ViewHelpers/Widget/PaginateAlphabeticalViewHelper.php](Classes/ViewHelpers/Widget/PaginateAlphabeticalViewHelper.php))
> Extends `AbstractViewHelper`; non-trivial alphabetical pagination with grouping and `LocalizationUtility::translate()` for labels — the pagination algorithm is unit-testable but `LocalizationUtility` must be mocked/stubbed; functional test needed for real Fluid rendering.

---

## Suitable for Functional Tests (22 classes)

**`ArgViewHelper`** ([Classes/ViewHelpers/ArgViewHelper.php](Classes/ViewHelpers/ArgViewHelper.php))
> Extends `AbstractViewHelper`; trivial argument pass-through — Fluid bootstrap required, no unit-testable logic.

**`ScriptViewHelper`** ([Classes/ViewHelpers/Asset/ScriptViewHelper.php](Classes/ViewHelpers/Asset/ScriptViewHelper.php))
> Extends `AbstractTagBasedViewHelper`; injects `CacheManager` (TYPO3 caching infrastructure) — requires full TYPO3 bootstrap.

**`StylesheetViewHelper`** ([Classes/ViewHelpers/Asset/StylesheetViewHelper.php](Classes/ViewHelpers/Asset/StylesheetViewHelper.php))
> Extends `AbstractTagBasedViewHelper`; injects `CacheManager` — caching integration mandates functional scope.

**`TcaFormFieldViewHelper`** ([Classes/ViewHelpers/Be/Form/TcaFormFieldViewHelper.php](Classes/ViewHelpers/Be/Form/TcaFormFieldViewHelper.php))
> Extends `AbstractViewHelper`; injects `GlobalsService`, uses `makeInstance(FormResultCompiler)` — depends on Backend form framework and globals.

**`IfIsAdminViewHelper`** ([Classes/ViewHelpers/Be/Security/IfIsAdminViewHelper.php](Classes/ViewHelpers/Be/Security/IfIsAdminViewHelper.php))
> Extends `AbstractConditionViewHelper`; reads `GlobalsService::getInstance()->getBackendUser()` — requires Backend context.

**`IfTableAccessViewHelper`** ([Classes/ViewHelpers/Be/Security/IfTableAccessViewHelper.php](Classes/ViewHelpers/Be/Security/IfTableAccessViewHelper.php))
> Extends `AbstractConditionViewHelper`; `GlobalsService::getInstance()->getBackendUser()->check(...)` and `makeInstance(DataMapper)` — Backend user + Extbase data mapper dependency.

**`AddCacheTagByNameViewHelper`** ([Classes/ViewHelpers/Cache/AddCacheTagByNameViewHelper.php](Classes/ViewHelpers/Cache/AddCacheTagByNameViewHelper.php))
> Extends `AbstractViewHelper`; adds TYPO3 frontend cache tags by name — requires FrontendInterface/caching bootstrap.

**`AddCacheTagViewHelper`** ([Classes/ViewHelpers/Cache/AddCacheTagViewHelper.php](Classes/ViewHelpers/Cache/AddCacheTagViewHelper.php))
> Extends `AbstractViewHelper`; adds TYPO3 frontend cache tags — caching infrastructure dependency.

**`CacheTagsViewHelper`** ([Classes/ViewHelpers/Debug/CacheTagsViewHelper.php](Classes/ViewHelpers/Debug/CacheTagsViewHelper.php))
> Extends `AbstractViewHelper`; debug display of active cache tags — reads from TYPO3 caching infrastructure.

**`StopViewHelper`** ([Classes/ViewHelpers/Debug/StopViewHelper.php](Classes/ViewHelpers/Debug/StopViewHelper.php))
> Extends `AbstractViewHelper`; halts Fluid template rendering — behaviour only verifiable within Fluid bootstrap.

**`ViewHelpersViewHelper`** ([Classes/ViewHelpers/Debug/ViewHelpersViewHelper.php](Classes/ViewHelpers/Debug/ViewHelpersViewHelper.php))
> Extends `AbstractViewHelper`; enumerates registered ViewHelpers via Fluid's internal registry — requires Fluid engine bootstrap.

**`SelectViewHelper`** ([Classes/ViewHelpers/Form/SelectViewHelper.php](Classes/ViewHelpers/Form/SelectViewHelper.php))
> Extends `AbstractFormFieldViewHelper`; Extbase form field — requires form framework bootstrap.

**`UploadViewHelper`** ([Classes/ViewHelpers/Form/UploadViewHelper.php](Classes/ViewHelpers/Form/UploadViewHelper.php))
> Extends `AbstractFormFieldViewHelper`; Extbase upload form field — form framework dependency.

**`FlexFormToArrayViewHelper`** ([Classes/ViewHelpers/Format/FlexFormToArrayViewHelper.php](Classes/ViewHelpers/Format/FlexFormToArrayViewHelper.php))
> Extends `AbstractViewHelper`; uses `makeInstance(FlexFormTools)` — depends on TYPO3 FlexForm parsing infrastructure.

**`StripSlashesViewHelper`** ([Classes/ViewHelpers/Format/StripSlashesViewHelper.php](Classes/ViewHelpers/Format/StripSlashesViewHelper.php))
> Extends `AbstractViewHelper`; wraps `stripslashes()` — logic is trivial (no unit value), Fluid rendering test suffices.

**`ContentValueViewHelper`** ([Classes/ViewHelpers/Get/ContentValueViewHelper.php](Classes/ViewHelpers/Get/ContentValueViewHelper.php))
> Extends `AbstractViewHelper`; uses `ConnectionPool` + `QueryBuilder` via `makeInstance` — database query requires functional scope.

**`TsfeViewHelper`** ([Classes/ViewHelpers/Get/TsfeViewHelper.php](Classes/ViewHelpers/Get/TsfeViewHelper.php))
> Extends `AbstractViewHelper`; accesses `$GLOBALS['TSFE']` — requires Frontend bootstrap.

**`SysLanguageUidViewHelper`** ([Classes/ViewHelpers/Meta/SysLanguageUidViewHelper.php](Classes/ViewHelpers/Meta/SysLanguageUidViewHelper.php))
> Extends `AbstractViewHelper`; returns `sys_language_uid` from TSFE/site context — runtime site context dependency.

**`TagViewHelper`** ([Classes/ViewHelpers/Meta/TagViewHelper.php](Classes/ViewHelpers/Meta/TagViewHelper.php))
> Extends `AbstractTagBasedViewHelper`; uses `makeInstance(PageRenderer)` to inject meta tags — PageRenderer is a TYPO3 singleton, functional scope required.

**`TitleTagViewHelper`** ([Classes/ViewHelpers/Meta/TitleTagViewHelper.php](Classes/ViewHelpers/Meta/TitleTagViewHelper.php))
> Extends `AbstractViewHelper`; uses `makeInstance(PageTitleViewHelperProvider)` — depends on TYPO3 page title infrastructure.

**`RemoveViewHelper`** ([Classes/ViewHelpers/Variable/RemoveViewHelper.php](Classes/ViewHelpers/Variable/RemoveViewHelper.php))
> Extends `AbstractViewHelper`; removes a variable from Fluid's `TemplateVariableContainer` — behaviour only meaningful in Fluid rendering context.

**`UpdateViewHelper`** ([Classes/ViewHelpers/Variable/UpdateViewHelper.php](Classes/ViewHelpers/Variable/UpdateViewHelper.php))
> Extends `AbstractViewHelper`; updates a variable in Fluid's `TemplateVariableContainer` — same Fluid infrastructure dependency as `RemoveViewHelper`.

---

## Not directly testable (3 classes)

**`ErrorHandlerInterface`** ([Classes/Dummy/ErrorHandlerInterface.php](Classes/Dummy/ErrorHandlerInterface.php))
> Interface — no implementation to test directly.

**`ServiceInterface`** ([Classes/Dummy/ServiceInterface.php](Classes/Dummy/ServiceInterface.php))
> Interface — no implementation to test directly.

**`WithCacheTagService`** ([Classes/ViewHelpers/Traits/WithCacheTagService.php](Classes/ViewHelpers/Traits/WithCacheTagService.php))
> Trait — tested indirectly through the ViewHelpers that use it.

---

## Priority recommendation

1. **Z1 Unit (Dummy classes)** — `Email`, `Greeter`, `ErrorHandler` are fastest to write and set up the test infrastructure baseline.
2. **Z2 Unit** — `Service` after `ErrorHandler` is tested (reuse the concrete or create a stub).
3. **Edge Z1 (Format/string ViewHelpers)** — `CalcViewHelper`, `JsonDecodeViewHelper`, `JsonEncodeViewHelper`, `RoundViewHelper`, `NumberViewHelper`, `ExplodeViewHelper`, `StripSlashesViewHelper`-equivalent logic — high ROI, pure PHP algorithms.
4. **Edge Z1 (collection/logic ViewHelpers)** — `ContainsViewHelper`, `ForViewHelper`, `GetViewHelper`, `SortObjectStorageViewHelper`, `ExistsViewHelper` — medium complexity, good coverage return.
5. **Edge Z2** — `PaginateAlphabeticalViewHelper` — mock `LocalizationUtility` via `GeneralUtility::addInstance` or static call isolation.
6. **Functional** — `ContentValueViewHelper` (DB query), `IfIsAdminViewHelper`/`IfTableAccessViewHelper` (BE user) — highest setup cost; defer until unit coverage is solid.
7. **De-prioritize** — `ArgViewHelper`, `StopViewHelper`, `RemoveViewHelper`, `UpdateViewHelper` — glue/pass-through with no algorithmic value.
