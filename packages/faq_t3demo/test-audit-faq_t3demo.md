---
title: "Test Audit – faq_t3demo"
date: 2026-06-04
time: "12:41:15"
extension: faq_t3demo
classes_total: 4
unit: 2
edge: 0
functional: 1
not_testable: 1
---

# Test Audit: faq_t3demo
# Generated: 2026-06-04 · 12:41:15

## Summary
| Category                       | Count |
|-------------------------------|-------|
| PHP files total               | 4     |
| Suitable for Unit Tests       | 2     |
| Edge case (both possible)     | 0     |
| Suitable for Functional Tests | 1     |
| Not directly testable         | 1     |

## Suitable for Unit Tests (2)
**`Greeter`** (Classes/Dummy/Greeter.php) — Plain PHP class in the `Dummy/` testing-baseline directory; no TYPO3 subsystem signals detected — fully isolatable unit test candidate.

**`DataHandlerCacheHook`** (Classes/Hooks/DataHandlerCacheHook.php) — DataHandler hook with constructor injection; no ConnectionPool, QueryBuilder, makeInstance, CacheManager, or TSFE signals detected — logic is isolated enough for unit testing.

## Suitable for Functional Tests (1)
**`DatabaseRecordList`** (Classes/RecordList/DatabaseRecordList.php) — Extends TYPO3's `\TYPO3\CMS\Backend\RecordList\DatabaseRecordList` and invokes `getQueryBuilder`, which ties it to the database layer; requires a functional test environment with a real DB.

## Not directly testable (1)
**`FaqController`** (Classes/Controller/FaqController.php) — Extbase backend controller; uses `BackendUserAuthentication`, `makeInstance(DatabaseRecordList::class)`, and `BackendUtility::readPageAccess()` — heavy TYPO3 infrastructure coupling makes unit testing impractical; functional test or integration test required.

## Priority recommendation
1. **`DataHandlerCacheHook`** — already has unit tests; highest-ROI hook logic is covered.
2. **`Greeter`** — new Unit candidate; add tests if the class carries any non-trivial logic.
3. **`DatabaseRecordList`** — functional test against a real DB to verify the overridden query behaviour.
4. **`FaqController`** — de-prioritise; controller pass-through with backend user/permission checks that belong in functional/acceptance tests.
