# Crop Variants

## Add Crop Variants

```php
$GLOBALS['TYPO3_CONF_VARS']['GFX']['cropVariantImports'][] = 'EXT:path/to/CropVariants.yaml';
```

If you are still using the old way `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['hfcore']['cropVariants']`, you should migrate.
Both ways still work though.

## Old Vs. New

### Old

The old system still works. You don't need to upgrade.

```yaml
cropVariantOverrides:
  - table: 'tt_content'
    type: 'hero_image'
    column: 'assets'
    cropVariants:
      default: { disabled: true }
      desktop:
        title: Headerimage
        selectedRatio: '16:5'
        allowedAspectRatios: ['16:5', NaN]
      tablet:
        title: Tablet / Phone Format
        selectedRatio: '2:1'
        allowedAspectRatios: [ '2:1', NaN ]
```

You also had to register the aspect ratios:

```yaml
aspectRatios:
  '16:5':
    title: '16:5'
    value: 3.2
```

### New

The new system prevents duplicate config and makes it easier.

```yaml
cropVariantOverrides:
  - for:
      - table: 'tt_content'
        type: 'textmedia'
        column: 'assets'
      - table: 'tt_content'
        type: 'hero_image'
        column: 'assets'
    cropVariants:
      showDefaultVariant: false
      desktop:
        title: Headerimage
        selectedRatio: '16:5'
        allowedAspectRatios: ['16:5', NaN]
      tablet:
        title: Tablet / Phone Format
        selectedRatio: '2:1'
        allowedAspectRatios: [ '2:1', NaN ]
```

You don't need to register aspect ratios any more. If the label is formatted like `x:x`, 
it generates everything automatically.

You can however still register aspect ratios, if you e.g. want a custom label:

```yaml
aspectRatios:
  '16:5': 'LLL:EXT:path/to/file.xlf'
  '1:2': 'My Label'
```

The old way still works as well, if you want a custom key for the aspect ratio. (that is not `x:x`)
