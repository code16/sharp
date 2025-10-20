# Entity List

Class: `Code16\Sharp\Show\Fields\SharpShowEntityListField`

## Constructor

This field needs, as first parameter, either the entity key or the `SharpEntity` class that declares the Entity List which will be included in the Show Page.

For instance:

```php
SharpShowEntityListField::make('products')
```

or

```php
SharpShowEntityListField::make(ProductEntity::class)
```

::: warning
This last syntax is better in terms of DX (since it allows using the IDE to navigate to the Entity List implementation), but it won’t work in two specific cases: if you use a custom `SharpEntityResolver` or if you your Entity is declared with multiple keys.
:::

To handle special cases, you can provide a second string argument: the first argument is the field key (as referred in the layout), and the second argument is the related Entity List key:

```php
SharpShowEntityListField::make('order_products', 'products')
``` 

::: tip
Note that unlike every other Show Field, the `instance` of the Show don't have to expose an attribute named like that, since the Entity List data is gathered with a dedicated request.
:::

Entity List fields really are just regular Entity List presented in a Show page: we therefore need a full Entity List implementation, declared in an Entity. To scope the data to the `instance` of the Show, you can use `hideFilterWithValue()` (see below).

## Configuration

### `hideFilterWithValue(string $filterName, $value)`

This is the most important method of the field, since it will not only hide a filter but also set its value. The purpose is to allow to **scope the data to the instance** of the Show Page. For example, let’s say we display an Order and that we want to embed a list of its products:

```php
class OrderShow extends SharpShow
{
    // ...
    
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(
            SharpShowEntityListField::make(ProductEntity::class)
                ->hideFilterWithValue(OrderFilter::class, 64)
        );
    }
}
```

We defined here that we want to display the Entity List defined in the `ProductEntity`, with its `OrderFilter` filter (which must be declared as usual in the Entity List implementation) hidden AND valued to `64` when gathering the data. In short: we want the products for the order of id `64`.

::: tip 
Note on the filter name: passing its full classname will always work, but you can also directly pass its `key`, in case you defined one.
:::

You can pass a closure as the value, and it will contain the current Show instance id. In most cases, you'll have to write this:

```php
SharpShowEntityListField::make('products')
    ->hideFilterWithValue(OrderFilter::class, fn ($instanceId) => $instanceId);
```

One final note: sometimes the linked filter is really just a scope, never displayed to the user. In this case, it can be tedious to write a full implementation in the Entity List. In this situation, you can use the `HiddenFiler` class for the filter, passing a key:

```php
class OrderShow extends SharpShow
{
    // ...
    
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields->addField(
            SharpShowEntityListField::make('products')
                ->hideFilterWithValue('order', fn ($instanceId) => $instanceId);
        );
    }
}
```

```php
use \Code16\Sharp\EntityList\Filters\HiddenFilter;

class OrderProductList extends SharpEntityList
{
    // ...

    protected function getFilters(): ?array
    {
        return [
            HiddenFilter::make('order')
        ];
    }
    
    public function getListData(): array|Arrayable
    {
        return $this->transform(
            Products::query()
                ->forOrderId($this->queryParams->filterFor('order'))
                ->get()
        );
    }
}
```

### `hideEntityCommand(array|string $commands): self`

Use it to hide any entity command in this particular Entity List (useful when reusing an Entity List displayed elsewhere). This will apply before looking at authorizations.

### `hideInstanceCommand(array|string $commands): self`

Use it to hide any instance command in this particular Entity List (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showEntityState(bool $showEntityState = true): self`

Use it to show or hide the EntityState label and command (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showCreateButton(bool $showCreateButton = true): self`

Use it to show or hide the "create" button in this particular Entity List (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showReorderButton(bool $showReorderButton = true): self`

Use it to show or hide the reorder button in this particular Entity List (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showSearchField(bool $showSearchField = true): self`

Use it to show or hide the search field in this particular Entity List (useful when reusing an Entity List).

### `showCount(bool $showCount = true): self`

Use it to show or hide the count of items in the Entity List.

## Display in layout

To display your entity list in your show page's layout, you have to use the `addEntityListSection()` method in your Show Page's `buildShowLayout()` method.

```php
protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(function (ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function (ShowLayoutColumn $column) {
                        $column
                            ->withFields(categories: 5, author: 7)
                           // ...
                    })
                    ->addColumn(5, function (ShowLayoutColumn $column) {
                        // ...
                    });
            })
            ->addEntityListSection(ProductEntity::class);
    }
```

## Transformer

There is no transformer, since Sharp will NOT look for an attribute in the instance sent. The data of the Entity List is brought by a distinct XHR call, the same Sharp will use for any Entity List.
