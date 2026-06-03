# HF Library

Collection of TYPO3 helpers and tools

## Changelog
### 2.0.1
- php deprecation fixes

### 2.0.0
- Support for TYPO3 v13 and v14
- diverse functions instead of FrontendController or $GLOBALS['TSFE']
- FrontendSimulationUtility prepared and deactivated
- divers fixes in FrontendSimulationUtility

### 1.2.4
- PHP warning fix in simulateFrontendEnvironment

### 1.2.3
- Bugfix: simulateFrontendEnvironment adding routing (pageUid) and isPreview=false

### 1.1.9
- Bugfix: FrontendSimulationUtilty correct generate the Request
- Bugfix: TemplateEmailService is its used in Command Context 
- Bugfix: BaseRepository do not break if no ConfigurationManager is set. 

## Changelog
### 1.1.8
- Fixed: XML Constructor error

### 1.1.7
- Feature: every model implements JsonSerializable and callable with magic function toArray()
- Bugfix: function_exists instead of is_callable to check function gmp_cmp()

### 1.1.3
- Bugfix: fix contraints for alphabetical paginator

## Changelog
### 1.1.2
- Bugfix: the property layout in model Content is one type 'string'

### 1.1.1
- Bugfix method postCallActionMethod in BaseController 

### 1.1.0
- Initial release
