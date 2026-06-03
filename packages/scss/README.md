# SCSS

A custom scss compiler for TYPO3.

---

## Documentation

### Variables

Define variables in TypoScript:

```typo3_typoscript
plugin.tx_scss.variables {
  color = #ff0000
  font-size = 16px
}
```
```scss
body {
  color: $color;
  font-size: $font-size;
}
```

### Library Files

Define library files in TypoScript: (These files get included in every compiled file)

```typo3_typoscript
page.includeCSS {
    myFile = EXT:path/to/file.scss
    myFile.isLib = 1
    
    # Optional, the higher the order, the later the file gets included. so you can override variables
    myFile.order = 5
}
```

### Caching

If you have a list of scss imports, prefix the variable with `// @scss-import-list` to correctly clear the cache.

---

## Changelog

*This changelog uses semantic versioning V2*
### 3.1.1
#### Fixed
- php nullable type fix

### 3.1.0
#### Added
- support for TYPO3 v13 und v14

### 3.0.4
#### Fixed
- boolean value for 2 variables

### 3.0.3
#### Fixed
- TYPO3 version dependency

### 3.0.2
#### Fixed
- cache not including libImports

### 3.0.1
#### Fixed
- removed const variable type

### 3.0.0
#### Added
- support for TYPO3 v13
#### Removed
- support for `plugin.tx_wsscss.variables` (use `plugin.tx_scss.variables` instead)
- drop support for TYPO3 v12
- ViewHelper
#### Changed
- renamed namespace to `Hausformat\Scss`
- renamed cache key to `hf_scss`
- use static cache key

### 2.4.0
#### Added
- added the option to prefix an import variable list with `// @scss-import-list` to correctly hash the files

### 2.3.0
#### Changed
- outsourced lib and variable logic into service
#### Fixed
- fixed not clearing cache when lib files change

### 2.2.1
#### Fixed
- fix sitePath (remove "Compiler.php")


### 2.2.0
#### Added
- functionality to replace relative file paths with it's public absolute url

### 2.1.0
#### Added
- order property for library files

### 2.0.0
#### Changed
- Use ws_scss version

### 1.0.1
#### Fixed
- Fixed cache file name

### 1.0.0
#### Added
- Initial release
