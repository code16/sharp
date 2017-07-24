# Building an Entity List

Let's start with the applicative code needed to show the list of `instances` for an `entity`. In the example shown in the configuration excerpt below, it's the `\App\Sharp\SpaceshipSharpList` class. 

    return [
        "entities" => [
            "spaceship" => [
                "list" => \App\Sharp\SpaceshipSharpList::class,
                "form" => \App\Sharp\SpaceshipSharpForm::class,
                "validator" => \App\Sharp\SpaceshipSharpValidator::class,
                "policy" => \App\Sharp\Policies\SpaceshipPolicy::class
            ]
        ]
    ];


We need to make it extend `Code16\Sharp\EntityList\SharpEntityList`, and therefore to implement 4 abstract methods:

## `buildListDataContainers()`

A "data container" is simply a column in the `Entity List`, named this way to abstract the presentation. This first function is responsible to describe each column:

    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("name")
                ->setLabel("Full name")
                ->setSortable()
                ->setHtml()
        )->addDataContainer([...]);
    }

Setting the label, allowing the column to be sortable and to display html is optionnal.

## `buildListLayout()`

Next step, define how those columns are displayed:

    function buildListLayout()
    {
        $this->addColumn("picture", 1, 2)
            ->addColumn("name", 9, 10)
            ->addColumnLarge("capacity", 2);
    }

We add columns giving:

- the column key, which must match those defined in `buildListDataContainers()`,
- the "width" of the column, as an integer on a 12-based grid,
- and the 2nd integer in the width on small screen.

In this example, `picture` and `name` will be displayed respectively on 1/12 and 9/12 of the viewport width on large screens, and 2/12 and 10/12 on small screens. The third column, `capacity`, will only be shown on large screens, with a width of 2/12.

## `getListData(EntityListQueryParams $params)`

Now the real work: grab and return the actual list data. This method must return an array of `instances` of our `entity`. You can do this however you want, so let's see a generic example, and then how Sharp can simplify the work if you use Eloquent.


### The generic way

Simply return an array, with 2 rules:

- each item must define the keys declared in the `buildDatacontainer()` function,
- plus one attribute for the identifier, which is `id` by default (more on that later).

So for instance, if we defined 3 columns:

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

Of course, real code will imply some data request, in a DB, a file, ... The important thing is that Sharp won't care.


### Transformers

In a more realistic project, you'll want to transform your data before sending it to the front code. Sharp can help: use the  `Code16\Sharp\Utils\Transformers\WithCustomTransformers` trait in your class, and you gain access to a useful `setCustomTransformer()` method:

    function getListData(EntityListQueryParams $params)
    {
        // Sudo code to retreive instances.
        $spaceships = $this->repository->all();
        
        return $this->setCustomTransformer(
            "capacity", 
            function($spaceship) {
                return (spaceship->capacity/1000) . "k";
            })
        )->transform($spaceships);
    }

The `setCustomTransformer()` function takes the key of the attribute to transform, and either a `Closure` or the full name of a class which must implement `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer`.

The `transform` function must be called after, and will apply all transformers on your list.

> Note that transformers need your models (spaceships, here) to allow a direct access to their attributes, like for instance `spaceship->capacity`, and to implement `Illuminate\Contracts\Support\Arrayable` interface. Eloquent Model fulfill those needs.


#### The ":" operator in transformers

If you need to reference a related attribute, like for instance the name of the author of a Post, you can define a custom transformer, or simply use the `:` operator, like this in `buildListDataContainers()` and `buildListLayout()`:

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

Then, with `WithCustomTransformers` trait, the `$post->author->name` attribute will be used.


### Handle query `$params`

As you may have noticed, `getListData()` accepts as an argument a `EntityListQueryParams` instance. This object will be filled by Sharp with query params:

- sorting: `$params->sortedBy()` and `$params->sortedDir()`
- search: `$params->hasSearch()` and `$params->searchWords()`
- filters: `$params->filterFor($filter)`

If the Entity List was configured to handle sort, filters or search (see below to learn how), and if the user performed such an action, values will be accessible here.

#### Sort

`$params->sortedBy()` contains the name of the attribute, and `$params->sortedDir()` the direction: `asc` or `desc`.

Note that the ability of sorting a column is defined in `buildListDataContainers()`.

#### Search

`$params->hasSearch()` returns true if the user entered a search, and `$params->searchWords()` return an array of search terms. This last method can take parameters, here's its full signature:

    public function searchWords(
        $isLike = true,
        $handleStar = true,
        $noStarTermPrefix = '%',
        $noStarTermSuffix = '%'
    )

- `$isLike`: if true, each term will be surrounded by `%` (by default).
- `$handleStar`: if true, and if a char `*` is found in a term, it will be replaced by `%` (default), and this term won't be surrounded by `%` (to allow "starts with" or "ends with" searches).
- `$noStarTermPrefix` and `$noStarTermSuffix`: the char to use in a `$isLike` case.

Here's a code sample with an Eloquent Model:

    if ($params->hasSearch()) {
        foreach ($params->searchWords() as $word) {
            $spaceships->where(function ($query) use ($word) {
                $query->orWhere('name', 'like', $word)
                    ->orWhere('pilots.name', 'like', $word);
                });
            }
        }
    }

#### Filters

We haven't see yet how we can build a `Filter`, but at this stage, a filter has simply a `key` and a `value`. So we can grab this calling `$filterValue = $params->filterFor($filterKey)`, and use the value in our query code.


### The Eloquent way

TODO `setUploadTransformer` ?


### Pagination

It's very common to return in `getListData()` paginated results:  simply return a `Illuminate\Contracts\Pagination\LengthAwarePaginator` in this case.

With `Eloquent` or the `QueryBuilder`, this means simply call `->paginate($count)` on the query.



## `buildListConfig()`


