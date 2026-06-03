---
title: "Test Audit – hf-lib"
date: 2026-06-01
extension: hf-lib
classes_total: 68
unit: 17
unit_z1: 16
unit_z2: 1
edge: 9
edge_z1: 3
edge_z2: 6
functional: 28
not_testable: 14
---

# Test Audit: `hf-lib`

*Generated: 2026-06-01 · 22:51:05*

## Summary

| Category                          | Count |
|----------------------------------|-------|
| PHP files total                  | 68    |
| Suitable for Unit Tests          | 17    |
| — Z1 (no stub/mock)              | 16    |
| — Z2 (stub/mock needed)          | 1     |
| Edge case (both possible)        | 9     |
| — Z1 (no stub/mock)              | 3     |
| — Z2 (stub/mock needed)          | 6     |
| Suitable for Functional Tests    | 28    |
| Not directly testable            | 14    |

---

## Suitable for Unit Tests (17 classes)

#### Z1 — No stub/mock needed (16)

**`GenericException`** ([Classes/Domain/Exception/GenericException.php](Classes/Domain/Exception/GenericException.php))
> Plain PHP exception extending `AbstractException` (which itself extends `\Exception`). No constructor parameters or injected dependencies — assert message and code directly.

**`Orientation`** ([Classes/Domain/Type/Orientation.php](Classes/Domain/Type/Orientation.php))
> Domain type (likely a PHP BackedEnum). Pure value with no dependencies — assert cases and values directly.

**`PassthroughValueMapper`** ([Classes/Routing/Aspect/PassthroughValueMapper.php](Classes/Routing/Aspect/PassthroughValueMapper.php))
> Implements `StaticMappableAspectInterface`, constructor takes `array $settings = []` (primitive). Simple pass-through routing mapper with no injected interfaces — assert `resolve()` and `generate()` return values directly.

**`CacheTagNamingService`** ([Classes/Service/Cache/CacheTagNamingService.php](Classes/Service/Cache/CacheTagNamingService.php))
> No constructor injection signals; produces cache tag name strings from inputs. Pure string-munging logic — assert output patterns directly.

**`ShowLayoutDisplayCondition`** ([Classes/UserFunctions/ShowLayoutDisplayCondition.php](Classes/UserFunctions/ShowLayoutDisplayCondition.php))
> No TYPO3 integration signals. User function for TCA display conditions — pure boolean evaluation logic, assert return values directly.

**`CaseTransformationUtility`** ([Classes/Utility/CaseTransformationUtility.php](Classes/Utility/CaseTransformationUtility.php))
> No constructor or injected dependencies. Pure string case transformation (camelCase, snake_case, etc.) — assert input/output pairs directly.

**`DateUtility`** ([Classes/Utility/DateUtility.php](Classes/Utility/DateUtility.php))
> No constructor or injected dependencies. Pure date calculation and formatting helpers — assert computed results directly.

**`DivUtility`** ([Classes/Utility/DivUtility.php](Classes/Utility/DivUtility.php))
> No constructor or injected dependencies. General-purpose utility (div/array helpers) — assert outputs directly.

**`DownloadUtility`** ([Classes/Utility/Resource/DownloadUtility.php](Classes/Utility/Resource/DownloadUtility.php))
> No TYPO3 integration signals found. Pure utility methods for constructing download-related values — assert outputs directly.

**`OrientationUtility`** ([Classes/Utility/Resource/OrientationUtility.php](Classes/Utility/Resource/OrientationUtility.php))
> No constructor or injected dependencies. Pure orientation calculation from image dimensions — assert return values directly.

**`StringUtility`** ([Classes/Utility/StringUtility.php](Classes/Utility/StringUtility.php))
> No constructor or injected dependencies. Pure string transformation and helper methods — assert input/output pairs directly.

**`EnableFieldsTcaUtility`** ([Classes/Utility/Tca/EnableFieldsTcaUtility.php](Classes/Utility/Tca/EnableFieldsTcaUtility.php))
> No TYPO3 integration signals. Returns static TCA `enablefields` configuration arrays — assert array structure directly.

**`GeneralTcaUtility`** ([Classes/Utility/Tca/GeneralTcaUtility.php](Classes/Utility/Tca/GeneralTcaUtility.php))
> No TYPO3 integration signals. Returns general TCA configuration arrays — assert array contents directly.

**`LanguageTcaUtility`** ([Classes/Utility/Tca/LanguageTcaUtility.php](Classes/Utility/Tca/LanguageTcaUtility.php))
> No TYPO3 integration signals. Returns language-related TCA field configurations — assert array structure directly.

**`TcaUtility`** ([Classes/Utility/TcaUtility.php](Classes/Utility/TcaUtility.php))
> No TYPO3 integration signals. General TCA builder utility — assert produced TCA arrays directly.

**`XmlToArrayUtility`** ([Classes/Utility/XmlToArrayUtility.php](Classes/Utility/XmlToArrayUtility.php))
> Constructor takes `string $xml = ''` (primitive only). Pure XML-to-array transformation using PHP's `DOMDocument`/`SimpleXML` — assert parsed array structure from known XML inputs directly.

#### Z2 — Stub or mock required (1)

**`ObjectStorageUtility`** ([Classes/Utility/ObjectStorageUtility.php](Classes/Utility/ObjectStorageUtility.php))
> No constructor injection, but uses `GeneralUtility::makeInstance(ObjectStorage::class)` internally to create new collection instances. Inject test doubles via `GeneralUtility::addInstance()` in setUp; the utility's sorting/filtering logic on `ObjectStorage` is pure data-munging worth unit-testing.

---

## Edge case – both Unit and Functional viable (9 classes)

#### Z1 — No stub/mock needed (3)

**`FrontendUser`** ([Classes/Domain/Model/FrontendUser.php](Classes/Domain/Model/FrontendUser.php))
> Constructor takes `($username = '', $password = '')` — primitives only, no injected interfaces. Implements `\JsonSerializable`, so `jsonSerialize()` logic is worth unit-testing. Edge case: being an Extbase entity, a functional test verifying real persistence mapping also adds value.

**`FrontendUserGroup`** ([Classes/Domain/Model/FrontendUserGroup.php](Classes/Domain/Model/FrontendUserGroup.php))
> Constructor takes `($title = '')` — primitive only. Implements `\JsonSerializable`. Edge case: functional test for real persistence mapping alongside unit test for `jsonSerialize()` logic.

**`Pages`** ([Classes/Domain/Model/Pages.php](Classes/Domain/Model/Pages.php))
> No constructor injection signals. Implements `\JsonSerializable`. Edge case: functional test for real page record mapping alongside unit test for serialization logic.

#### Z2 — Stub or mock required (6)

**`Context`** ([Classes/Context/Context.php](Classes/Context/Context.php))
> Constructor takes `?TYPO3\CMS\Core\Context\Context $context = null`; when `null`, falls back to `makeInstance`. The injected TYPO3 `Context` must be stubbed to control aspect responses. Edge case: functional test verifies real TYPO3 context integration (frontend/backend user aspects).

**`Content`** ([Classes/Domain/Model/Content.php](Classes/Domain/Model/Content.php))
> Constructor calls `makeInstance(ObjectStorage::class)` for four properties (`assets`, `categories`, `media`, `image`). Inject test doubles via `GeneralUtility::addInstance()`. Implements `\JsonSerializable` — `jsonSerialize()` logic warrants a unit test. Edge case: functional test for real Extbase persistence mapping.

**`HfbaseObjectValidator`** ([Classes/Domain/Validator/HfbaseObjectValidator.php](Classes/Domain/Validator/HfbaseObjectValidator.php))
> Extends `AbstractValidator`. Uses `makeInstance` for dynamically resolved validator classes and `Error` objects. Inject test doubles via `GeneralUtility::addInstance()`. The validation-config-processing loop is non-trivial logic worth unit-testing. Edge case: functional test verifies real validator resolution from DI container.

**`AlphabeticalPaginator`** ([Classes/Pagination/AlphabeticalPaginator.php](Classes/Pagination/AlphabeticalPaginator.php))
> Constructor takes `QueryResultInterface $queryResult` — must be stubbed with `createStub()` to feed controlled results. The alphabetical grouping and pagination algorithm is pure data-munging. Edge case: functional test with real query results verifies full integration with TYPO3's paginator stack.

**`TreeableService`** ([Classes/Service/TreeableService.php](Classes/Service/TreeableService.php))
> Implements `SingletonInterface`. Main methods (`createTreeFromRoot`, `createTree`, `createSubTree`) accept optional `TreeableRepositoryInterface` — must be stubbed to control which items are returned and test depth-limiting branches. Edge case: functional test builds a real category tree from DB fixtures.

**`FolderUtility`** ([Classes/Utility/Resource/FolderUtility.php](Classes/Utility/Resource/FolderUtility.php))
> Uses `makeInstance(FileInfo::class, $path)` where `FileInfo` is a `SplFileInfo` subclass (plain PHP helper, not a TYPO3 subsystem service). Inject test double via `GeneralUtility::addInstance()`. Edge case: functional test verifies real filesystem interactions.

---

## Suitable for Functional Tests (28 classes)

**`YamlFileLoader`** ([Classes/Configuration/Loader/YamlFileLoader.php](Classes/Configuration/Loader/YamlFileLoader.php))
> Wraps TYPO3's core `\TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader` via `makeInstance`. Requires TYPO3's file loading infrastructure — set up real YAML fixtures and verify parsed arrays.

**`Category`** ([Classes/Domain/Model/Category.php](Classes/Domain/Model/Category.php))
> Extends TYPO3's `SysCategory`, uses `makeInstance(TreeableService::class)` (TYPO3 service) and `makeInstance(ObjectStorage::class)`. The tree-building logic depends on a real TYPO3 service — load DB fixtures, verify tree hierarchy.

**`FileReference`** ([Classes/Domain/Model/FileReference.php](Classes/Domain/Model/FileReference.php))
> Extends TYPO3's `FileReference`, uses `makeInstance(FileRepository::class)` (TYPO3 repository) repeatedly across many accessor methods. Requires TYPO3's FAL infrastructure — set up file records, verify accessor output.

**`CategoryRepository`** ([Classes/Domain/Repository/CategoryRepository.php](Classes/Domain/Repository/CategoryRepository.php))
> Extends `BaseRepository`, implements `TreeableRepositoryInterface`. Uses `makeInstance(Typo3QuerySettings::class)` and extends Extbase Repository. Requires real DB — load category fixtures, verify tree queries.

**`ContentRepository`** ([Classes/Domain/Repository/ContentRepository.php](Classes/Domain/Repository/ContentRepository.php))
> Extends `BaseRepository`. Extbase repository with DB access — load `tt_content` fixtures, verify custom query methods.

**`FileReferenceRepository`** ([Classes/Domain/Repository/FileReferenceRepository.php](Classes/Domain/Repository/FileReferenceRepository.php))
> Extends `BaseRepository`. Extbase repository for file references — load FAL fixtures, verify queries.

**`FrontendUserGroupRepository`** ([Classes/Domain/Repository/FrontendUserGroupRepository.php](Classes/Domain/Repository/FrontendUserGroupRepository.php))
> Extends TYPO3's `Repository`. Extbase repository — load fe_groups fixtures, verify queries.

**`FrontendUserRepository`** ([Classes/Domain/Repository/FrontendUserRepository.php](Classes/Domain/Repository/FrontendUserRepository.php))
> Extends `BaseRepository`. Extbase repository — load fe_users fixtures, verify custom finders.

**`PagesRepository`** ([Classes/Domain/Repository/PagesRepository.php](Classes/Domain/Repository/PagesRepository.php))
> Extends `BaseRepository`. Extbase repository for pages — load pages fixtures, verify queries.

**`ItemsProcFunc`** ([Classes/Hooks/ItemsProcFunc.php](Classes/Hooks/ItemsProcFunc.php))
> Uses `GlobalsService` (reads `$GLOBALS['BE_USER']`, `$GLOBALS['TYPO3_CONF_VARS']`), `LocalizationUtility`, and `makeInstance(ConfigurationManager::class)`. Requires full TYPO3 backend bootstrap — verify items array manipulation in a real TCA context.

**`PageTitleViewHelperProvider`** ([Classes/Seo/PageTitleViewHelperProvider.php](Classes/Seo/PageTitleViewHelperProvider.php))
> Extends TYPO3's `AbstractPageTitleProvider`. Requires TYPO3 frontend bootstrap to set/retrieve page title — verify title propagation in a functional test.

**`BackendFieldRenderService`** ([Classes/Service/BackendFieldRenderService.php](Classes/Service/BackendFieldRenderService.php))
> Uses `makeInstance` for `TcaDatabaseRecord`, `FormDataCompiler`, `NodeFactory` — all deep TYPO3 backend form-rendering subsystem services. Requires full backend bootstrap with DB.

**`AutomaticCacheClearingService`** ([Classes/Service/Cache/AutomaticCacheClearingService.php](Classes/Service/Cache/AutomaticCacheClearingService.php))
> Injected `CacheManager` via `injectCacheManager`. TYPO3 caching infrastructure dependency — verify cache tag flushing in a real cache context.

**`ObjectCacheTagService`** ([Classes/Service/Cache/ObjectCacheTagService.php](Classes/Service/Cache/ObjectCacheTagService.php))
> Extends `AbstractCacheTagService`, injected `GlobalsService` via `injectGlobalsService`. `GlobalsService` accesses TYPO3 globals — requires TYPO3 bootstrap to verify tag generation.

**`PagesCacheTagService`** ([Classes/Service/Cache/PagesCacheTagService.php](Classes/Service/Cache/PagesCacheTagService.php))
> Extends `AbstractCacheTagService`, injected `GlobalsService` via `injectGlobalsService`. Requires TYPO3 bootstrap — verify page-level cache tag generation.

**`FrontendUserService`** ([Classes/Service/FrontendUserService.php](Classes/Service/FrontendUserService.php))
> Implements `FrontendUserServiceInterface`. Uses `makeInstance(Context::class)` to read the TYPO3 `frontend.user` aspect. Requires TYPO3 frontend bootstrap — verify logged-in user detection.

**`GlobalsService`** ([Classes/Service/GlobalsService.php](Classes/Service/GlobalsService.php))
> Implements `SingletonInterface`. Exposes `getBackendUser()`, `getFrontendUser()`, `getLanguageService()`, `getConfVars()`, etc. — wraps `$GLOBALS`. Requires full TYPO3 bootstrap — verify correct global proxying per context.

**`LanguageService`** ([Classes/Service/LanguageService.php](Classes/Service/LanguageService.php))
> Uses `makeInstance(LanguageServiceFactory::class)` and delegates to `GlobalsService::getInstance()->getLanguageService()`. Requires TYPO3 language bootstrap — verify label resolution.

**`FileReferenceService`** ([Classes/Service/Resource/FileReferenceService.php](Classes/Service/Resource/FileReferenceService.php))
> Injected `PersistenceManagerInterface` via `injectPersistenceManager`. Uses Extbase persistence — set up real file reference records, verify creation and persistence.

**`FileService`** ([Classes/Service/Resource/FileService.php](Classes/Service/Resource/FileService.php))
> Uses `makeInstance` for `StorageRepository`, `ResourceFactory`, `FileIndexRepository`, `DataMapper`, `MetaDataRepository`. Deep TYPO3 FAL subsystem — set up file storage fixtures, verify file operations.

**`FolderService`** ([Classes/Service/Resource/FolderService.php](Classes/Service/Resource/FolderService.php))
> Uses `makeInstance(StorageRepository::class)`. Requires TYPO3 FAL storage infrastructure — verify folder creation and retrieval.

**`SimulationService`** ([Classes/Service/SimulationService.php](Classes/Service/SimulationService.php))
> Uses `makeInstance` for `ServerRequestFactoryInterface`, `ConfigurationManager`, `SiteFinder`. Bootstraps a simulated TYPO3 frontend — verify site/language context simulation.

**`SlugService`** ([Classes/Service/SlugService.php](Classes/Service/SlugService.php))
> Uses `ConnectionPool` / `QueryBuilder` directly for uniqueness checks. Requires real DB — load page/record fixtures, verify slug generation and uniqueness enforcement.

**`TemplateEmailService`** ([Classes/Service/TemplateEmailService.php](Classes/Service/TemplateEmailService.php))
> Uses `makeInstance(MailMessage::class)` and `Environment::getPublicPath()`. Requires TYPO3 mail infrastructure — verify message assembly from Fluid templates in a functional context.

**`TypoScriptService`** ([Classes/Service/TypoScriptService.php](Classes/Service/TypoScriptService.php))
> Implements `TypoScriptServiceInterface`. Wraps TYPO3 TypoScript configuration — requires full TYPO3 frontend bootstrap to resolve TypoScript settings.

**`ClearCacheUtility`** ([Classes/Utility/Cache/ClearCacheUtility.php](Classes/Utility/Cache/ClearCacheUtility.php))
> Uses `makeInstance(DataHandler::class)` and `makeInstance(BackendUserAuthentication::class)`. Requires TYPO3 backend subsystem with real DB to trigger cache clearing.

**`FrontendSimulationUtility`** ([Classes/Utility/FrontendSimulationUtility.php](Classes/Utility/FrontendSimulationUtility.php))
> Manipulates `$GLOBALS['TSFE']` directly and uses `makeInstance` for `PageArguments`, `FrontendUserAuthentication`, `SiteRouteResult`, `ServerRequest`. Core TYPO3 frontend simulation — requires full TYPO3 bootstrap to verify context setup and teardown.

**`TypoScriptUtility`** ([Classes/Utility/TypoScriptUtility.php](Classes/Utility/TypoScriptUtility.php))
> Uses `GlobalsService::getInstance()->hasGlobal('TSFE')` and `makeInstance(ConfigurationManagerInterface::class)`. Depends on active TYPO3 frontend context — verify TypoScript resolution in a functional test.

---

## Not directly testable (14 classes)

**`BaseCommand`** ([Classes/Command/BaseCommand.php](Classes/Command/BaseCommand.php))
> Abstract class extending Symfony's `Command`. Tested indirectly via concrete command subclasses.

**`BaseController`** ([Classes/Controller/BaseController.php](Classes/Controller/BaseController.php))
> Abstract Extbase `ActionController` subclass. Tested indirectly via concrete controller subclasses in functional tests.

**`AbstractException` / `AbstractExceptionInterface`** ([Classes/Domain/Exception/AbstractException.php](Classes/Domain/Exception/AbstractException.php))
> File contains both a PHP interface and an abstract class. The interface defines contracts only; the abstract class is tested via subclasses (e.g. `GenericException`).

**`AbstractEntity`** ([Classes/Domain/Model/AbstractEntity.php](Classes/Domain/Model/AbstractEntity.php))
> Abstract Extbase entity. Tested indirectly via concrete model subclasses.

**`SortableInterface`** ([Classes/Domain/Model/Interfaces/SortableInterface.php](Classes/Domain/Model/Interfaces/SortableInterface.php))
> PHP interface — defines sorting contract only; tested through implementing classes.

**`TranslatableInterface`** ([Classes/Domain/Model/Interfaces/TranslatableInterface.php](Classes/Domain/Model/Interfaces/TranslatableInterface.php))
> PHP interface — defines translation contract only; tested through implementing classes.

**`TreeableInterface`** ([Classes/Domain/Model/Interfaces/TreeableInterface.php](Classes/Domain/Model/Interfaces/TreeableInterface.php))
> PHP interface — defines tree structure contract; tested through implementing classes.

**`BaseRepository`** ([Classes/Domain/Repository/BaseRepository.php](Classes/Domain/Repository/BaseRepository.php))
> Abstract Extbase repository with `ConnectionPool`/`QueryBuilder`. Tested indirectly via concrete repository subclasses in functional tests.

**`TreeableRepositoryInterface`** ([Classes/Domain/Repository/Interfaces/TreeableRepositoryInterface.php](Classes/Domain/Repository/Interfaces/TreeableRepositoryInterface.php))
> PHP interface — defines tree repository contract; tested through implementing classes.

**`AbstractCacheTagService`** ([Classes/Service/Cache/AbstractCacheTagService.php](Classes/Service/Cache/AbstractCacheTagService.php))
> Abstract class. Tested indirectly via `ObjectCacheTagService` and `PagesCacheTagService`.

**`CacheTagServiceInterface`** ([Classes/Service/Cache/CacheTagServiceInterface.php](Classes/Service/Cache/CacheTagServiceInterface.php))
> PHP interface — defines cache tag contract; tested through implementing classes.

**`FrontendUserServiceInterface`** ([Classes/Service/Interfaces/FrontendUserServiceInterface.php](Classes/Service/Interfaces/FrontendUserServiceInterface.php))
> PHP interface — defines frontend user service contract; tested through `FrontendUserService`.

**`MailServiceInterface`** ([Classes/Service/Interfaces/MailServiceInterface.php](Classes/Service/Interfaces/MailServiceInterface.php))
> PHP interface — defines mail service contract; tested through implementing classes.

**`TypoScriptServiceInterface`** ([Classes/Service/Interfaces/TypoScriptServiceInterface.php](Classes/Service/Interfaces/TypoScriptServiceInterface.php))
> PHP interface — defines TypoScript service contract; tested through `TypoScriptService`.

---

## Notes

- **`BaseController`** is classic Extbase controller glue — it wires repositories, PersistenceManager, and view together. Do **not** invest unit test effort here; its subclasses belong in functional tests.
- **`BaseRepository`** and its concrete subclasses are pure persistence glue. Unit-testing query construction with mocked QueryBuilder produces brittle tests; functional tests with DB fixtures give far more value.
- **`GlobalsService`** is the central TYPO3 globals wrapper — it cannot be meaningfully unit-tested; always functional.
- The five TCA utility classes (`TcaUtility`, `EnableFieldsTcaUtility`, `GeneralTcaUtility`, `LanguageTcaUtility`, `Utility/Tca/*`) are the fastest unit test wins in this extension: static array builders with no dependencies.

---

## Priority recommendation

1. **TCA utility classes (Z1) — highest ROI:** `EnableFieldsTcaUtility`, `GeneralTcaUtility`, `LanguageTcaUtility`, `TcaUtility` — static array builders, zero setup, immediate regression protection for TCA changes.
2. **Data-munging utilities (Z1):** `CaseTransformationUtility`, `StringUtility`, `DateUtility`, `XmlToArrayUtility`, `OrientationUtility` — pure functions, trivial to test, high coverage per line of test code.
3. **`ObjectStorageUtility` (Z2):** Collection manipulation logic; use `GeneralUtility::addInstance()` for `ObjectStorage` — medium effort, good ROI.
4. **Edge-case model serialization (Z1):** `FrontendUser`, `FrontendUserGroup`, `Pages` — unit-test the `jsonSerialize()` logic, then add a functional test for persistence mapping.
5. **`AlphabeticalPaginator` + `TreeableService` (Edge Z2):** Non-trivial algorithms; stub the `QueryResultInterface` / `TreeableRepositoryInterface` for unit tests, then add a functional test with real DB fixtures.
6. **Repository functional tests:** `CategoryRepository`, `ContentRepository`, `FrontendUserRepository` — load DB fixtures, verify custom finders; medium effort, high integration value.
7. **De-prioritise:** `BaseController` subclass actions, model getters/setters without logic, `GlobalsService` (functional-only), `FrontendSimulationUtility` (deep bootstrap required).
