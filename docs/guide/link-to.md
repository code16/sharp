# Create links to an entity

You may need to create a link to an entity, either to an EntityList, a Show or a Form.

## Classes
Depending on your target, you'll want to use either:

- `Code16\Sharp\Utils\Links\LinkToEntityList`
- `Code16\Sharp\Utils\Links\LinkToForm`
- `Code16\Sharp\Utils\Links\LinkToShowPage`
- `Code16\Sharp\Utils\Links\LinkToSingleShowPage`
- `Code16\Sharp\Utils\Links\LinkToSingleForm`

To create an instance, use the static `make` method, which may take one or two arguments:

- For `LinkToEntityList`, `LinkToSingleShowPage` and `LinkToSingleForm`: `::make($entityKey)`
- For `LinkToForm` and `LinkToShowPage`: `::make($entityKey, $instanceId)`

## Example: link use case

This can be use for instance in a custom transformer of an EntityList column; imagine we want to list the pilots of a spaceship in a column, and link each one to its form: 

```php
// In an EntityList:

function getListData(EntityListQueryParams $params)
{
    [...]

    return $this
        ->setCustomTransformer("pilots", function($pilots, $spaceship) {
            return $spaceship->pilots->map(function($pilot) {
                return LinkToForm::make("pilot", $pilot->id)
                    ->setEntityKey("pilot")
                    ->renderAsText($pilot->name); // This will render a full <a...> tag
            })->implode("<br>");
        })
        ->transform(
            $spaceships->paginate(10)
        );
}
```
                        
## URL use case

Sometimes you'll only need the URL, and not the `<a>` tag: call `$link->renderAsUrl()` in this case.


## Common methods

### `renderAsText(string $text)`

Render the link as a `<a>` tag.

### `renderAsUrl()`

Render the link as an URL (string).

### `setTooltip(string $toltip)`

Set a link tooltip (only when rendered as link).


## Methods of `LinkToEntityList`

### `setSearch(string $searchText)`

Set a search text.

### `addFilter(string $filterFullClassNameOrKey, string $value)`

Set a filter and its value; for the filter, you can either pass its custom key or (more conveniently) its full class name.

### `setSort(string $attribute, $dir = 'asc')`

Set a default sort.

### `setFullQuerystring(array $querystring)`

To manually build the querystring (which you should avoid).


## Methods of `LinkToForm`

### `throughShowPage($throughShowPage = true)`

To generate a list > show > form breadcrumb, instead of (by default) just a list > form.

