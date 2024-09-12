# Autocomplete

Classes: `Code16\Sharp\Form\Fields\SharpFormAutocompleteLocalField` and `Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField` 

## Configuration for local autocomplete

### `setLocalValues($localValues)`

Set the values of the dictionary on mode=local, as an object array with at least an `id` attribute (or the `setItemIdAttribute` value).

### `setLocalSearchKeys(array $searchKeys)`

Set the names of the attributes used in the search (mode=local).
Default: `['value']`

### `setLocalValuesLinkedTo(string ...$fieldKeys)`

This method is useful to link the dataset of a local autocomplete (aka: the `localValues`) to another form field. Please refer to [the documentation of the select field's `setOptionsLinkedTo()` method](select.md), which is identical.

## Configuration for remote autocomplete

### `setRemoteEndpoint(string $remoteEndpoint)`

The endpoint to hit with mode=remote.

If this endpoint is yours (`remote` mode here is useful to avoid loading a lot of data in the view), you can add the `sharp_auth` middleware to the API route to handle authentication and prevent this API endpoint to be called by non-sharp users:

```php
Route::get('/api/sharp/clients')
    ->middleware('sharp_auth')
    ->uses('MySharpApiClientController@index')
```

### `setSearchMinChars(int $searchMinChars)`

Set a minimum number of character to type before performing the search.
Default: `1`

### `setRemoteSearchAttribute(string $remoteSearchAttribute)`

The attribute name sent to the remote endpoint as search key.
Default: `'query'`

### `setDataWrapper($dataWrapper)`

Configure an optional dataWrapper to handle results sent in a wrapper, typically "data". 
Default: empty string.

### `setDebounceDelayInMilliseconds($debounceDelay)`

Configure the debounce delay between each endpoint call
Default: 300.

### `setRemoteMethodGET()`
### `setRemoteMethodPOST()`

Set the remote method to GET (default) or POST.

### `setDynamicRemoteEndpoint(string $dynamicRemoteEndpoint, array $defaultValues)`

In a remote autocomplete case, you can use this method instead of `setRemoteEndpoint` to handle a dynamic URL, based on another form field. Here's how, for example:

```php
SharpFormAutocompleteField::make('brand', 'remote')
    ->setDynamicRemoteEndpoint('/brands/{{country}}');
```

In this example, the `{{country}}` placeholder will be replaced by the value of the `country` form field. You can define multiple replacements if necessary.

You may need to provide a default value for the endpoint, used when `country` (in our example) is not valued (without default, the autocomplete field will be displayed as disabled). To do that,
fill the second argument:

```php
SharpFormAutocompleteField::make('model', 'remote')
    ->setDynamicRemoteEndpoint(''/models/{{country}}/{{brand}}'', [
        'country' => 'france',
        'brand' => 'renault'
    ]);
```

The default endpoint would be `/brands/france/renault`.


## Common configuration for both modes

### `setItemIdAttribute(string $itemIdAttribute)`

Set the name of the id attribute for items. This is useful :
- if you pass an object as the data for the autocomplete (meaning: in the formatter's `toFront`).
- to designate the id attribute in the remote API call return.
Default: `"id"`

### `setListItemInlineTemplate(string $template)`
### `setResultItemInlineTemplate(string $template)`
Just write the template as a string, using placeholders for data like this: `{{var}}`.

Example:

```php
$panel->setInlineTemplate(
    'Foreground: <strong>{{color}}</strong>'
)
```

The template will be used, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).

Be aware that you'll need for this to work to pass a valuated object to the Autocomplete, as data.

### `setListItemTemplatePath(string $listItemTemplatePath)`
### `setResultItemTemplatePath(string $resultItemTemplate)`

Use this if you need more control: give the path of a full template, in its own file.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

```vue
<div v-if="show">result is {{value}}</div>
<div v-else>result is unknown</div>
```

The template will be used, depending on the function, to display either the list item (in the result dropdown) or the result item (meaning the valuated form input).

Be aware that you'll need for this to work to pass a valuated object to the Autocomplete, as data.

### `setAdditionalTemplateData(array $data)`

Useful to add some static (or at least not instance-dependant) data to the template. For instance:

```php
SharpFormAutocompleteRemoteField::make('brand')
    ->setAdditionalTemplateData([
        'years' => [2020, 1018, 2017]
    ]);
```

In the template, the provided data can be used as normal:

```vue
<div v-for="year in years"> {{ year }} </div>
```

## Formatter

### `toFront`

If **mode=local**, you must pass there either:
- a single id, since the label will be grabbed from the `localValues` array,
- or an object with an `id` (or whatever was configured through `setItemIdAttribute()`) property.

If **mode=remote**, you must pass an object with at least an `id` (or whatever was configured through `setItemIdAttribute()`) attribute and all attributes needed by the item templates.

### `fromFront`

Returns the selected item id.
