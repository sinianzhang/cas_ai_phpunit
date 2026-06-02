# Breaking Changes

## hfbase => 1.0.0

### ViewHelpers that were removed

- `hf:format.strReplace` => use `f:replace` instead
- `hf:container.getCType` => use `hf:get.contentValue` instead
- `hf:meta.addJs` => use `hf:asset.script` instead
- `hf:string.contains` => use `hf:contains` instead
- `hf:inHaystack` => use `hf:contains` instead
- `hf:feLogin` => use `f:security.ifHasRole` and `f:security.ifAuthenticated` instead
- `hf:widget.paginateAlphabetical`

### ViewHelpers that were moved

- `hf:case` => `hf:format.case`
- `hf:format.stripslashes` => `hf:format.stripSlashes`
- `hf:date.format` => `hf:format.date`
- `hf:cleanHtml` => `hf:format.cleanHtml`
- `hf:numberFormat` => `hf:format.number`
- `hf:solr.facetFor` => moved to hf-solr-mod extension
