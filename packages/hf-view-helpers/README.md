### claude plugin
- claude --plugin-dir .claude/plugins/typo3-test-audit

### Run a specific test
- ddev exec php vendor/bin/phpunit -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml packages/hf-view-helpers/Tests/Unit/Dummy/EmailTest.php --filter xxxx

### phpStan for all unit test files in the extension "hf-viewh-helpers" (loading config file)
- ddev php vendor/bin/phpstan analyse -c packages/hf-view-helpers/Build/phpstan/phpstan.tests.neon [--generate-baseline packages/hf-view-helpers/Build/phpstan/phpstan-baseline.neon]

### phpStan for one unit test file in the extension "hf-viewh-helpers" (loading config file)
- ddev php vendor/bin/phpstan analyse -c packages/hf-view-helpers/Build/phpstan/phpstan.tests.neon \
  packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/CaseViewHelperTest.php

### coverage for the extension "hf-viewh-helpers" (output format: text or html)
- ddev xdebug on
- ddev exec XDEBUG_MODE=coverage php vendor/bin/phpunit \
  -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
  [--coverage-text|--coverage-html packages/hf-view-helpers/Build/phpunit/coverage]
- ddev xdebug off

### coverage for one class in the extension "hf-viewh-helpers" (z.B. Klasse: CaseViewHelper)
- ddev xdebug on
- ddev exec XDEBUG_MODE=coverage php vendor/bin/phpunit \
  -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
  --coverage-text \
  packages/hf-view-helpers/Tests/Unit/ViewHelpers/Format/CaseViewHelperTest.php
- ddev xdebug off

### mutation test for one class in the extension "hf-viewh-helpers" (z.B. Klasse: CaseViewHelper)
- ddev xdebug on
- ddev exec php vendor/bin/infection run \
  --configuration=packages/hf-view-helpers/Build/infection/infection.json5  --threads=4 \
  --filter packages/hf-view-helpers/Classes/ViewHelpers/Format/CaseViewHelper.php
- ddev xdebug off

# HF View Helpers

A collection of TYPO3 ViewHelpers.

## Changelog
### 2.0.4
### Fixed
- JsonEncodeViewHelper: value of type mixed

### 2.0.3
### Fixed
- getPublicFilePath() in ScriptViewHelper and StylesheetViewHelper

### 2.0.2
### Fixed
- type check for bool, int and float in ArgViewHelper

### 2.0.1
### Fixed
- request in ViewHelper instead of $GLOBALS['TSFE']


### 2.0.0
### Added
- Support for TYPO3 v13 and v14

### 1.1.0
#### Added
- Widget/PaginateAlphabeticalViewHelper readded from previews hfbase-Version


### 1.0.1

#### Changed
- fixed Asset/ScriptViewHelper 
- fixed Asset/StylesheetViewHelper

#### Added
- "inline" Attribute to Asset/ScriptViewHelper

### 1.0.0

See [Breaking Changes](./BREAKING.md)

#### Changed
- use `render` instead of `renderStatic`
- moved `case` to `format.case`
- moved `format.stripslashes` to `format.stripSlashes`
- moved `date.format` to `format.date`
- moved `numberFormat` to `format.number`
- moved `cleanHtml` to `format.cleanHtml`

#### Added
- ability to use value arg in `format.whitespace`
- added `contains` ViewHelper
- added tests for several ViewHelpers
