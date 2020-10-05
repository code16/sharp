# Create a link to an entity

You'll need, sometimes, to create a link to an entity, either a list or a form. For this, you can use a utility class: `Code16\Sharp\Utils\LinkToEntity`.

## Link use case

This can be use for instance in a custom transformer of an EntityList column; imagine we want to list the pilots of a spaceship in a column, and link each one to its form: 

```php
// In an EntityList:

function getListData(EntityListQueryParams $params)
{
    [...]

    return $this
        ->setCustomTransformer("pilots", function($pilots, $spaceship) {
            return $spaceship->pilots->map(function($pilot) {
                return (new LinkToEntity($pilot->name))
                    ->setEntityKey("pilot")
                    ->toFormOfInstance($pilot->id) // We could use instead toShowOfInstance() to link the Show Page
                    ->render(); // This will render a full <a...> tag
            })->implode("<br>");
        })
        ->transform(
            $spaceships->paginate(10)
        );
}
```
                        
## URL use case

Sometimes you'll only need the URL, and not the `<a>` tag: call `$link->renderAsUrl()` in this case.

## Methods

### `setEntityKey(string $key)`

The `$key` must obviously correspond to a configured entity (in config).

### `setSearch(string $searchText)`

Set a search text (only for EntityList).

### `addFilter($name, $value)`

Set a filter and its value (only EntityList).

### `setSort($attribute, $dir = 'asc')`

Set a default sort (only EntityList).

### `toFormOfInstance($instanceId)`

Set an instance id, and change the link destination to a Form.

### `toShowOfInstance($instanceId)`

Set an instance id, and change the link destination to a Show.

### `toSingleForm()`

Change the link destination to a Single Form.

### `toSingleShow()`

Change the link destination to a Single Show.

### `setTooltip(string $toltip)`

Set a link tooltip (only when rendered as link).

### `setFullQuerystring($querystring)`

Define the whole querystring manually (only for special cases). 