# Building an Entity List

We need an Entity List to display the list of `instances` for an `entity`. This list can be paginated, searchable, filtered, ... as we'll see below.

## Write the class

First let's write the applicative class, and make it extend `Code16\Sharp\EntityList\SharpEntityList`. Therefore there are four abstract methods to implement:

- `buildListDataContainers()` and `buildListLayout()` for the structure, 
- `getListData(EntityListQueryParams $params)` for the data,
- and `buildListConfig()` for... the list config.

Each one is detailed here:

### `buildListDataContainers()`

A "data container" is simply a column in the `Entity List`, named this way to abstract the presentation. This first function is responsible to describe each column:

```php
    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("name")
                ->setLabel("Full name")
                ->setSortable()
                ->setHtml()
        )->addDataContainer([...]);
    }
```

Setting the label, allowing the column to be sortable and to display html is optionnal.

### `buildListLayout()`

Next step, define how those columns are displayed:

```php
    function buildListLayout()
    {
        $this->addColumn("picture", 1, 2)
            ->addColumn("name", 9, 10)
            ->addColumnLarge("capacity", 2);
    }
```

We add columns giving:

- the column key, which must match those defined in `buildListDataContainers()`,
- the "width" of the column, as an integer on a 12-based grid,
- and the 2nd integer in the width on small screen.

In this example, `picture` and `name` will be displayed respectively on 1/12 and 9/12 of the viewport width on large screens, and 2/12 and 10/12 on small screens. The third column, `capacity`, will only be shown on large screens, with a width of 2/12.

### `getListData(EntityListQueryParams $params)`

Now the real work: grab and return the actual list data. This method must return an array of `instances` of our `entity`. You can do this however you want, so let's see a generic example:

The returned array is meant to be build with 2 rules:

- each item must define the keys declared in the `buildDatacontainer()` function,
- plus one attribute for the identifier, which is `id` by default (more on that later).

So for instance, if we defined 2 columns `name` and `capacity`:

```php
    function getListData(EntityListQueryParams $params)
    {
	    return [
            [
                "id" => 1,
                "name" => "USS Enterprise",
                "capacity" => "10k"
            ], [
                "id" => 2,
                "name" => "USS Agamemnon",
                "capacity" => "20k"			
            ]
        ];
    }
```

Of course, real code would imply some data request in a DB, or a file for instance; the important thing is that Sharp don't care.


#### Transformers

In a more realistic project, you'll want to transform your data before sending it to the front code. Sharp can help with that, as explained in the detailled [How to transform data](how-to-transform-data.md) documentation.

#### Handle query `$params`

As you may have noticed, `getListData()` accepts as an argument a `EntityListQueryParams` instance. This object will be filled by Sharp with query params:

- sorting: `$params->sortedBy()` and `$params->sortedDir()`
- search: `$params->hasSearch()` and `$params->searchWords()`
- filters: `$params->filterFor($filter)`

If the Entity List was configured to handle sort, filters or search (see below to learn how), and if the user performed such an action, values will be accessible here.

##### Sort

`$params->sortedBy()` contains the name of the attribute, and `$params->sortedDir()` the direction: `asc` or `desc`.

Note that the ability of sorting a column is defined in `buildListDataContainers()`.

##### Search

`$params->hasSearch()` returns true if the user entered a search, and `$params->searchWords()` returns an array of search terms. This last method can take parameters, here's its full signature:

```php
    public function searchWords(
        $isLike = true,
        $handleStar = true,
        $noStarTermPrefix = '%',
        $noStarTermSuffix = '%'
    )
```

- `$isLike`: if true, each term will be surrounded by `%` (by default).
- `$handleStar`: if true, and if a char `*` is found in a term, it will be replaced by `%` (default), and this term won't be surrounded by `%` (to allow "starts with" or "ends with" searches).
- `$noStarTermPrefix` and `$noStarTermSuffix`: the char to use in a `$isLike` case.

Here's a code sample with an Eloquent Model:

```php
    if ($params->hasSearch()) {
        foreach ($params->searchWords() as $word) {
            $spaceships->where(function ($query) use ($word) {
                $query->orWhere('name', 'like', $word)
                    ->orWhere('pilots.name', 'like', $word);
                });
            }
        }
    }
```

##### Filters

We haven't see yet how we can build a `Filter`, but at this stage, a filter is simply a `key` and a `value`. So we can grab this calling `$filterValue = $params->filterFor($filterKey)`, and use the value in our query code.

#### Pagination

It's very common to return in `getListData()` paginated results:  simply return a `Illuminate\Contracts\Pagination\LengthAwarePaginator` in this case.

With `Eloquent` or the `QueryBuilder`, this means calling `->paginate($count)` on the query.

### `buildListConfig()`

Finally, this last function must describe... the list config. Let's see an example:

```php
    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            ->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setPaginated();
    }
```

Here we declare that:

- each item of our list is identified by an attribute `id` (this is the default value);
- the list is meant to allow search to the user, meaning Sharp will display a search text input and process its content to fill the `EntityListQueryParams` instance (see above);
- the list must be sorted by "name", meaning that the `EntityListQueryParams` instance will be filled with this default value;
- and finally, the list is paginated, meaning that `getListData(EntityListQueryParams $params)` must return an instance of `LengthAwarePaginator` (see above) and that Sharp will display pagination links if needed.

This config can also contain things related to Filters, Commands or State, and all of this is discussed on following chapters.


## Configure the entity

In the sharp config file, we have to declare our entity, and link it to the Entity List class:

```php
    // config/sharp.php
    
    return [
        "entities" => [
            "spaceship" => [
                "list" => \App\Sharp\SpaceshipSharpList::class,
            ]
        ]
    ];
```

Then we can access the Entity List at the following URL:
**/sharp/list/spaceship** (replace "spaceship" by our entity key).

To go ahead and learn how to add a link in the Sharp side menu, [look here](building-menu.md).

---

> next chapter: [Filters](filters.md).