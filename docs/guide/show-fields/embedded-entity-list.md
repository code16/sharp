# Embedded EntityList

Class: `Code16\Sharp\Show\Fields\SharpShowEntityListField`

## Constructor

This field needs two keys: 
- the name of the local field key, that will be used in the Show layout to display the Entity List,
- and the entity key as defined in sharp's config, fot the Entity that declares an Entity List. 

For instance:

```php
SharpShowEntityListField::make('products', 'product')
```

... where `products` is the field key and `product` is the related Entity List key. In the very common case where the two keys are the same, you can omit the second argument; the local field key is only used for the layout: unlike every other field, the `instance` of the Show don't have to expose an attribute named like that, since the EntityList data is gathered with a dedicated request.

Embedded Entity List are really just regular Entity List presented in a Show page: we therefore need a full Entity List implementation, declared in an Entity. To scope the data to the `instance` of the Show, use `hideFilterWithValue()` (see below).

## Configuration

### `hideFilterWithValue(string $filterName, $value)`

This is maybe the most important method of the field, since it will not only hide a filter, but also set its value. The purpose is to allow to scope the data to the instance of the Show Page. For instance, let's say we display an Order and that we want to embed a list of products:

```php
SharpShowEntityListField::make('products')
    ->hideFilterWithValue(OrderFilter::class, 64);
```

We defined here that we want a `products` fields related to an Entity List which implementation class is defined in the `products` Entity, and its `OrderFilter` filter (which must be declared as usual in the Entity List implementation) must be hidden AND valued to `64` when gathering the data. In short: we want the products for the order of id `64`.

::: tip Note on the filter name: passing its full classname will always work, but you can also directly pass its `key`, in case you defined one.
:::

You can pass a closure as the value, and it will contain the current Show instance id. In most cases, you'll have to write this:

```php
SharpShowEntityListField::make('products')
    ->hideFilterWithValue(OrderFilter::class, fn ($instanceId) => $instanceId);
```

### `hideEntityCommand(array|string $commands): self`

Use it to hide any entity command in this particular embedded Entity List (useful when reusing an EntityList). This will apply before looking at authorizations.

### `hideInstanceCommand(array|string $commands): self`

Use it to hide any instance command in this particular embedded Entity List (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showEntityState(bool $showEntityState = true): self`

Use it to show or hide the EntityState label and command (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showCreateButton(bool $showCreateButton = true): self`

Use it to show or hide the create button in this particular embedded Entity List (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showReorderButton(bool $showReorderButton = true): self`

Use it to show or hide the reorder button in this particular embedded Entity List (useful when reusing an Entity List). This will apply before looking at authorizations.

### `showSearchField(bool $showSearchField = true): self`

Use it to show or hide the search field in this particular embedded Entity List (useful when reusing an Entity List).

### `showCount(bool $showCount = true): self`

Use it to show or hide the count of items in the embedded Entity List.

## Transformer

There is no transformer, since Sharp will NOT look for an attribute in the instance sent. The data of the Entity List is brought by a distinct XHR call, the same Sharp will use for a non embedded Entity List.

## A note on extending existing Entity List

You can of course build a dedicated Entity List, but in many cases it may be easier to extend one already needed in the main menu. In our products / order example for instance, we can have a full products EntityList, presented in the main menu, with all products and some general commands, and another one for the order Show Page which extends the first one and maybe redefine its layout, or allow to reorder in this case it's useful.