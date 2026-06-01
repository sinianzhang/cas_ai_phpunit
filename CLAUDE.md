# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TYPO3 14.3 CMS demo project (based on demo.typo3.org) running under DDEV. PHP 8.3, MariaDB 10.11, nginx-fpm. The project is used as a learning environment for PHPUnit testing with TYPO3.

## Common Commands

All commands run inside the DDEV container via `ddev exec` or using DDEV wrappers:

```bash
# Start/stop local environment
ddev start
ddev stop

# Composer
ddev composer install
ddev composer require <package>

# TYPO3 CLI
ddev typo3 database:updateschema
ddev typo3 backend:createadmin <username> <password>
ddev typo3 cache:flush

# Static analysis
ddev exec bin/phpstan analyse

# Database import
ddev import-db < db_dump.sql
```

## Local URLs

- Frontend: https://demo.typo3.org.ddev.site
- Backend: https://demo.typo3.org.ddev.site/typo3 (admin / Password.1)
- Install tool password: `joh316`

## Architecture

### Directory Layout

- `src/extensions/` — three local TYPO3 extensions (path-repository, loaded via `composer.json`)
- `web/` — TYPO3 document root (`typo3/`, `typo3conf/`, `fileadmin/`)
- `config/` — TYPO3 system config (`system/settings.php`, `system/additional.php`, `sites/`)
- `vendor/` — Composer dependencies including TYPO3 core packages
- `bin/` — CLI tools (`typo3`, `phpstan`, `fluid`, etc.)

### Local Extensions (`src/extensions/`)

| Extension key | Composer name | Purpose |
|---|---|---|
| `site_t3demo` | `b13/site-t3demo` | Main site package: TypoScript, page templates, content types, SCSS, backend layout configs |
| `faq_t3demo` | `b13/faq-t3demo` | Custom FAQ backend module with a dedicated database table (`tx_faqt3demo_faq`) |
| `demologin` | `b13/demologin` | Demo login provider allowing one-click backend login without a password |

### Configuration Context System

`config/system/additional.php` loads context-specific PHP files from `config/AdditionalConfiguration/` based on `TYPO3_CONTEXT`. DDEV sets `TYPO3_CONTEXT=Development/DDEV`, which loads both `DevelopmentContext.php` and `Development/DDEVContext.php`.

### site_t3demo Architecture

The site package is the central piece. Key relationships:
- **TypoScript** lives in `Configuration/Sets/Demo/TypoScript/` and is loaded via the Site Set `Configuration/Sets/Demo/config.yaml`
- **Page templates** use Fluid (`Resources/Private/Pages/`) with a Default layout, page-type-specific templates (Startpage, Contentpage, Faqpage, etc.), and shared partials (Header, Footer)
- **Content types** are custom CType definitions using TCA overrides in `Configuration/TCA/Overrides/` — each type has its own Fluid template in `Resources/Private/Contenttypes/`
- **Backend layouts** are configured via PageTsConfig in `Configuration/Sets/Demo/PageTsConfig/mod/web_layout/BackendLayouts/`
- **SCSS** source files are in `Resources/Private/Scss/`; compiled CSS is committed to `Resources/Public/Css/`

### TYPO3 PSR-4 Namespaces

- `B13\SiteT3demo\` → `src/extensions/site_t3demo/Classes/`
- `B13\FaqT3demo\` → `src/extensions/faq_t3demo/Classes/`
- `B13\DemoLogin\` → `src/extensions/demologin/Classes/`

### Key Vendor Extensions

- `b13/content-sync` — content synchronisation from the live TYPO3 demo server
- `b13/assetcollector` — asset management
- `helhum/typo3-console` — extended TYPO3 CLI (`ddev typo3 …`)
- `brotkrueml/schema` — structured data / JSON-LD
