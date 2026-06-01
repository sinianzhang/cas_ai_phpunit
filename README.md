# TYPO3 Demo Project for CAS AI PhpUnit

This is the GIT repository for CAS AI PHPUnit Project. 

## About the Demo Project

...

## Local Development

Requirements:
* DDEV, see [Get Started with DDEV](https://www.ddev.com/get-started/)
* Composer >= 2.5
* PHP >= 8.1

To set up the minimal TYPO3 Demo Project for local development

1. Download code `https://github.com/sinianzhang/cas_ai_phpunit.git`
2. Install composer packages `ddev composer install`
3. Get database and fileadmin using `ddev import-db < db_dump.sql` 
4. (optional) Update schema: `ddev typo3 database:updateschema`
5. (optional) Create a backend user `ddev typo3 backend:createadmin username password`
7. Start the project running `ddev start`

Test BE and FE
* Backend: https://demo.typo3.org.ddev.site/typo3
* Frontend: https://demo.typo3.org.ddev.site

Backend User
* Username: `admin`
* Password: `Password.1`

Install
* joh316

## Credits

The project was originally from TYPO3 Demo Project https://git.typo3.org/services/demo.typo3.org/site.
