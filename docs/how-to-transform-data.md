# How to transform data

Data transformation is useful when sending data to the front, which happens in the Entity List `getListData()` and in the Entity Form `find()` methods.

> Note that transformers need your data models to allow a direct access to their attributes, like for instance `spaceship->capacity`, and to implement `Illuminate\Contracts\Support\Arrayable` interface. Eloquent Model fulfill those needs.


## The `transform()` function

In an Entity List or an Entity Form, you can use the `transform()` function which will:

- apply all custom transformers on your list (see below),
- transform the given model(s) into an array, handling pagination if a `LengthAwarePaginator` is provided.

Eloquent example in an Entity List:

```php
    function getListData(EntityListQueryParams $params)
    {
        return $this->transform(
            Spaceship::with("picture", "type", "pilots")
                    ->paginate(10);
        );
    }
```

Eloquent example in an Entity Form:

```php
    function find($id): array
    {
        return $this->transform(
            Spaceship::with("reviews", "pilots")->findOrFail($id)
        );
    }
```

## Custom transformers

In the process, it's easy to add some custom transformation with `setCustomTransformer()`:

```php
    function getListData(EntityListQueryParams $params)
    {
        return $this->setCustomTransformer(
            "capacity", 
            function($capacity, $spaceship, $attribute) {
                return ($capacity/1000) . "k";
            })
        )->transform($spaceships);
    }
```

The `setCustomTransformer()` function takes the key of the attribute to transform, and either a `Closure`, an instance of a class which implements `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer`, or even just the full class name of the latest.


## Transform attribute of a related model (hasMany relationship)

Sometimes (maybe more often in the Entity Form), you would like to transform an attribute of a related model in a "has many" relationship. For instance let's say you want to display the names of the sons of a father in caps:

```php
    return $this->setCustomTransformer(
            "sons[name]", 
            function($son) {
                return strtoupper($son->name)
            })
        )->transform($father);
```

The convention in this case is to use an array notation, given that `$father->sons` is a collection of objects with a `name` attribute


## The ":" separator and transformers

Sometimes you'll need to reference a related attribute, like for instance the name of the author of a Post, either in an Entity List:

```php
    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("author:name")
                ->setLabel("Author")
        );
    }
    
    function buildListLayout()
    {
        $this->addColumn("author:name", 6, 6)
    }
```

or in an Entity Form:

```php
    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("picture:legend")
                ->setLabel("Legend")
        );
    }
```

The `:` separator used here will be interpreted in `transform()`, and the `$post->author->name` attribute will be used.


---

> next chapter: [Sharp built-in solution for uploads](sharp-built-in-solution-for-uploads.md).
