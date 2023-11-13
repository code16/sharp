# How to transform data

Data transformation is useful when sending data to the front, which happens in the Entity List `getListData()`, and in the Form or Show Page `find()` methods.

::: warning PRE-REQUISITES
Note that transformers need your data models to allow direct access to their attributes, like for instance `product->price`, and to implement `Illuminate\Contracts\Support\Arrayable` interface. Eloquent models fulfill those needs.
:::

## The `transform()` function

In an Entity List, a Show Page or a Form, you can use the `transform()` function which will:

- apply all custom transformers on your list (see below),
- transform the given model(s) into an array, handling pagination if a `LengthAwarePaginator` is provided.

Eloquent example in an Entity List:

```php
class ProductEntityList extends SharpEntityList
{
    // [...]
    
    public function getListData(): array|Arrayable
    {
        return $this->transform(
            Product::with('pictures')
                ->paginate(50)
        );
    }
}
```

Eloquent example in a Form (or a Show Page, they share the API):

```php
class ProductForm extends SharpForm
{
    // [...]
    
    public function find($id): array
    {
        return $this->transform(
            Product::findOrFail($id)
        );
    }
}
```

## Custom transformers

Wa can handle transformations with `setCustomTransformer()`:

```php
class ProductEntityList extends SharpEntityList
{
    // [...]
    
    function getListData(): array|Arrayable
    {
        return $this
            ->setCustomTransformer(
                'price',
                function ($price, $product, $attribute) {
                    return number_format($price, 2).' €';
                }
            )
            ->transform(
                Product::with('pictures')
                    ->paginate(50)
            );
    }
}
```

The `setCustomTransformer()` function takes the key of the attribute to transform, and either a `Closure`, an instance of a class which implements `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer`, or even just the full class name of the latest.

::: tip
Note that a custom transformer defined on a missing attribute will add the attribute to the result array. It's a convenient way to add a computed attribute, like for instance a `full_name` built with a bunch of real attributes.  
But if this isn't the wanted behaviour, the solution is to define in the `SharpAttributeTransformer` implementation a public `applyIfAttributeIsMissing()` function, which when returning `false` ensure that Sharp will ignore the attribute if it is missing.
:::

## Transform attribute of a related model (hasMany relationship)

Sometimes you would like to transform an attribute of a related model in a hasMany relationship. For instance let's say you want to display the names of the sons of a father in caps:

```php
return $this
    ->setCustomTransformer(
        "sons[name]",
        fn ($son) => strtoupper($son->name)
    )
    ->transform($father);
```

The convention in this case is to use an array notation, given that `$father->sons` is a collection of objects with a `name` attribute

## The ":" separator and transformers

Sometimes you'll need to reference a related attribute, like for instance the name of the author of a Post, either in an Entity List:

```php
class ProductEntityList extends SharpEntityList
{
    // [...]
    
    function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer->addField(
            EntityListDataContainer::make('author:name')
                ->setLabel('Author')
        );
    }
    
    function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn('author:name', 6);
    }
}
```

or in a Form / Show Page:

```php
class ProductForm extends SharpForm
{
    // [...]
    
    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('picture:legend')
                ->setLabel('Legend')
        );
    }
}
```

The `:` separator used here will be interpreted in `transform()`, and the `$post->author->name` attribute will be used.
