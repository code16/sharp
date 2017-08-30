# Form field: Autocomplete

Class: `Code16\Sharp\Form\Fields\SharpFormAutocompleteField`

## Configuration

### Constructor: `make(string $key, string $mode)`

`$mode` must be either "local" (dictionnary is defined locally with `setLocalValues()`) or "remote" (a endpoint must be provided).


### `setLocalValues($localValues)`

Set the values of the dictionnary on mode=local, as an key=>value array.

### `setSearchKeys(array $searchKeys)`

Set the names of the attributes used in the search (mode=local).
Default: `["value"]`

### `setSearchMinChars(int $searchMinChars)`

Set a miminum number of character to type before perfoming the search.
Default: `1`

### `setRemoteEndpoint(string $remoteEndpoint)`

The endpoint to hit with mode=remote.

### `setRemoteSearchAttribute(string $remoteSearchAttribute)`

The attribute name sent to the remote endpoint as search key.
Default: `"query"`

### `setRemoteMethodGET()` and `setRemoteMethodPOST()`

Set the remote method to GET (default) or POST.

### `setItemIdAttribute(string $itemIdAttribute)`

Set the name of the id attribute for items.
Default: `"id"`

### `setListItemInlineTemplate(string $template)` and `setResultItemInlineTemplate(string $template)`

Just write the template as a string, using placeholders for data like this: `{{var}}`.

Example:

    $panel->setInlineTemplate(
        "<h1>{{count}}</h1> spaceships in activity"
    )

The template will be use, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).


### `setListItemTemplatePath(string $listItemTemplatePath)` and `setResultItemTemplatePath(string $resultItemTemplate)`

Use this if you need more control: give the path of a full template, in its own file.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

`<div v-if="show">result is {{value}}</div>`
`<div v-else>result is unknown</div>`

The template will be use, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).


## Formatter

- `toFront`: expects the related item id.
- `fromFront`: returns the select item id.
