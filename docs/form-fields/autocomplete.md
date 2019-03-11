# Form field: Autocomplete

Class: `Code16\Sharp\Form\Fields\SharpFormAutocompleteField`

## Configuration

### Constructor: `make(string $key, string $mode)`

`$mode` must be either "local" (dictionnary is defined locally with `setLocalValues()`) or "remote" (a endpoint must be provided).


### `setLocalValues($localValues)`

Set the values of the dictionnary on mode=local, as an object array with at least an `id` attribute (or the `setItemIdAttribute` value).

### `setLocalSearchKeys(array $searchKeys)`

Set the names of the attributes used in the search (mode=local).
Default: `["value"]`

### `setSearchMinChars(int $searchMinChars)`

Set a miminum number of character to type before perfoming the search.
Default: `1`

### `setRemoteEndpoint(string $remoteEndpoint)`

The endpoint to hit with mode=remote.

If this endpoint is yours (`remote` mode here is useful to avoid loading a lot of data in the view), you can add the `sharp_auth` middleware to the API route to handle authentication and prevent this API endpoint to be called by non-sharp users:

```php
    Route::get('/api/sharp/clients')
        ->middleware('sharp_auth')
        ->uses("MySharpApiClientController@index")
```

### `setRemoteSearchAttribute(string $remoteSearchAttribute)`

The attribute name sent to the remote endpoint as search key.
Default: `"query"`

### `setRemoteMethodGET()` and `setRemoteMethodPOST()`

Set the remote method to GET (default) or POST.

### `setItemIdAttribute(string $itemIdAttribute)`

Set the name of the id attribute for items. This is useful :
- if you pass an object as the data for the autocomplete (meaning: in the formatter's `toFront`).
- to designate the id attribute in the remote API call return.
Default: `"id"`

### `setListItemInlineTemplate(string $template)` and `setResultItemInlineTemplate(string $template)`

Just write the template as a string, using placeholders for data like this: `{{var}}`.

Example:

```php
    $panel->setInlineTemplate(
        "Foreground: <strong>{{color}}</strong>"
    )
```

The template will be use, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).

Be aware that you'll need for this to work to pass a valuated object to the Autocomplete, as data.


### `setListItemTemplatePath(string $listItemTemplatePath)` and `setResultItemTemplatePath(string $resultItemTemplate)`

Use this if you need more control: give the path of a full template, in its own file.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

`<div v-if="show">result is {{value}}</div>`
`<div v-else>result is unknown</div>`

The template will be use, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).

Be aware that you'll need for this to work to pass a valuated object to the Autocomplete, as data.


## Formatter

### `toFront`

If **mode=local**, you must pass there either: 
- an single id, since the label will be grabbed from the `localValues` array,
- or an object with an `id` (or whatever was configure through `setItemIdAttribute()`) property.

If **mode=remote**, you must pass an object with at least an `id` (or whatever was configure through `setItemIdAttribute()`) attribute and all attributes needed by the item templates.


### `fromFront`

Returns the selected item id.
