### claude plugin
- claude --plugin-dir .claude/plugins/typo3-test-audit

### Run a specific test
- ddev exec php vendor/bin/phpunit -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml packages/hf-view-helpers/Tests/Unit/Dummy/EmailTest.php --filter xxxx

### phpStan
- ddev php vendor/bin/phpstan analyse -c packages/hf-view-helpers/Build/phpstan/phpstan.tests.neon [--generate-baseline packages/hf-view-helpers/Build/phpstan/phpstan-baseline.neon] -vvv

### run coverage
- ddev xdebug on
- ddev exec php vendor/bin/phpunit \
  -c packages/hf-view-helpers/Build/phpunit/UnitTests.xml \
  --coverage-html packages/hf-view-helpers/Build/phpunit/coverage
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
