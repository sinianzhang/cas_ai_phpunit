# TYPO3 Demo Project for CAS AI PhpUnit

This is the GIT repository for CAS AI PHPUnit Project. 

## Credits
The project was originally from TYPO3 Demo Project https://git.typo3.org/services/demo.typo3.org/site.


## About the Demo Project and Plugin mit 3 skills

Plugin with 3 Claude Kills

(1) test-audit-text: creates a md-file as audit report for the given extension, and creats a txt-file listing all classes, which are suitable for PhpUnitTest

(2) test-audit-chart (optional): creates a svg-file with a pie chart/diagram

(3) generate-unit-tests: creates missing PhpUnitTest files or extends existing PhpUnitTest files according to given comments as hints

## Local Development

Requirements:
* DDEV, see [Get Started with DDEV](https://www.ddev.com/get-started/)
* Composer >= 2.5
* PHP >= 8.1

To set up the minimal TYPO3 Demo Project for local development

1. Download code `git clone https://github.com/sinianzhang/cas_ai_phpunit.git`

2. Go into project and install composer packages `cd cas_ai_phpunit` and `ddev composer install`

3. Get database and fileadmin using `ddev import-db < db_dump.sql` 

4. (optional) Update schema: `ddev typo3 database:updateschema`

5. (optional) Create a backend user `ddev typo3 backend:createadmin username password`

6. (optional) Re-Start the project running `ddev restart`

7. (optional) Create a local setting file `.claude/settings.local.json` in order to run skill without asking confirmation 
```json
{
  "permissions": {
    "allow": [
      "Bash(*)",
      "Read(*)",
      "Write(*)",
      "Edit(*)"
    ]
  }
}
```

Test BE and FE
* Backend: https://demo.typo3.org.ddev.site/typo3
* Frontend: https://demo.typo3.org.ddev.site

Backend User
* Username: `admin`
* Password: `Password.1`

Install
* joh316

## Plugin with 3 Claude Kills (!!! Skills must be run one after the other !!!)
Load claude plugin 'typo3-test-audit'
* `claude --plugin-dir .claude/plugins/typo3-test-audit`

(1) test-audit-text
* `/typo3-test-audit:test-audit-text [extension name, e.g. faq_t3demo]`

(2) test-audit-chart (optional)
* `/typo3-test-audit:test-audit-chart [extension name, e.g. faq_t3demo]` 

(3) generate-unit-tests
* `/typo3-test-audit:generate-unit-tests [extension name, e.g. faq_t3demo]`

## PhpUnitTest and PhpStan
Run PhpUnitTest
* e.g. using a config file `ddev exec php vendor/bin/phpunit -c packages/faq_t3demo/Build/phpunit/UnitTests.xml`

Run PhpStan
* e.g. using a config file `ddev php vendor/bin/phpstan analyse -c packages/faq_t3demo/Build/phpstan/phpstan.tests.neon`



