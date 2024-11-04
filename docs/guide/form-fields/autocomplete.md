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

The remote endpoint which should return JSON-formatted results. Note that you can add the `sharp_auth` middleware to this route to handle authentication and prevent this API endpoint to be called by non-sharp users:

```php
// in a route file

Route::get('/api/sharp/clients', [MySharpApiClientController::class, 'index'])
    ->middleware('sharp_auth');
```

::: tip
This endpoint MUST be part of your application. If you need to hit an external endpoint, you should create a custom endpoint in your application that will call the external endpoint (be sure to check the alternative `setRemoteCallback` method).
:::

### `setRemoteCallback(Closure $closure, ?array $linkedFields = null)`

To avoid the pain of writing a new dedicated endpoint, and for simple cases, you can use this method to provide a callback that will be called when the autocomplete field needs to fetch data. The callback will receive the search string as a parameter and should return an array of objects.

Example:

```php
SharpFormAutocompleteRemoteField::make('customer')
    ->setRemoteCallback(function ($search) {
        return Customer::select('id', 'name', 'email')
            ->where('name', 'like', "%$search%")
            ->get();
    });
```

The second argument, `$linkedFields`, allows you to provide a list of fields that will be sent with their values to the callback, so you can filter the results based on the values of other fields.

Example:

```php
SharpFormAutocompleteRemoteField::make('customer')
    ->setRemoteCallback(function ($search, $linkedFields) {
        return Customer::select('id', 'name', 'email')
            ->when(
                $linkedFields['country'], 
                fn ($query) => $query->where('country_id', $linkedFields['country'])
            )
            ->where('name', 'like', "%$search%")
            ->get();
    }, linkedFields: ['country']);
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
SharpFormAutocompleteRemoteField::make('brand')
    ->setDynamicRemoteEndpoint('/brands/{{country}}');
```

In this example, the `{{country}}` placeholder will be replaced by the value of the `country` form field. You can define multiple replacements if necessary.

You may need to provide a default value for the endpoint, used when `country` (in our example) is not valued (without default, the autocomplete field will be displayed as disabled). To do that,
fill the second argument:

```php
SharpFormAutocompleteRemoteField::make('model')
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

### `setListItemTemplate(View|string $template)`
### `setResultItemTemplate(View|string $template)`

The templates for the list and result items can be set in two ways: either by passing a string, or by passing a Laravel view.

Examples:

```php
SharpFormAutocompleteRemoteField::make('customer')
    ->setRemoteCallback(function ($search) {
        return Customer::select('id', 'name', 'email')
            ->where('name', 'like', "%$search%")
            ->get();
    })
    ->setListItemTemplate('<div>{{$name}}</div><div><small>{{$email}}</small></div>')
    ->setResultItemTemplate(view('my/customer/blade/view'));
```

## Formatter

### `toFront`

If **mode=local**, you must pass there either:
- a single id, since the label will be grabbed from the `localValues` array,
- or an object with an `id` (or whatever was configured through `setItemIdAttribute()`) property.

If **mode=remote**, you must pass an object with at least an `id` (or whatever was configured through `setItemIdAttribute()`) attribute and all attributes needed by the item templates.

### `fromFront`

Returns the selected item id.
