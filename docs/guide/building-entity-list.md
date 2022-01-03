---
sidebarDepth: 3
---

# Building an Entity List

We need an Entity List to display the list of `instances` for an `entity`. This list can be paginated, searchable, filtered, ... as we'll see below.

## Generator

```bash
php artisan sharp:make:entity-list <class_name> [--model=<model_name>]
```

## Write the class

First let's write the applicative class, and make it extend `Code16\Sharp\EntityList\SharpEntityList`. Therefore, there are four abstract methods to implement:

- `buildListFields(EntityListFieldsContainer $fieldsContainer)` and `buildListLayout(EntityListFieldsLayout $fieldsLayout)` for the structure,

- `getListData()` for the data,

- and `buildListConfig()` for... the list config.

Each one is detailed here:

### `buildListFields(EntityListFieldsContainer $fieldsContainer)`

A field is a column in the `Entity List`. This first function is responsible to describe each column:

```php
function buildListFields(EntityListFieldsContainer $fieldsContainer)
{
    $fieldsContainer
        ->addField(
            EntityListField::make("name")
                ->setLabel("Full name")
                ->setSortable()
                ->setHtml()
        )
        ->addField([...]);
}
```

Setting the label, allowing the column to be sortable and to display html is optional.

### `buildListLayout(EntityListFieldsLayout $fieldsLayout)`

Next step, define how those columns are displayed:

```php
function buildListLayout(EntityListFieldsLayout $fieldsLayout)
{
    $fieldsLayout->addColumn("picture", 1)
        ->addColumn("name")
        ->addColumnLarge("capacity", 2);
}
```

We add columns giving:

- the column key, which must match those defined in `buildListFields()`,

- and the "width" of the column, as an integer on a 12-based grid. If missing, it will be deduced.

In this example, `picture` and `capacity` will be displayed respectively on 1/12 and 2/12 of the viewport width. The column `name` will fill the rest, 9/12.

To handle small screens, you can declare an optional `buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout)` function: 

```php
function buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout)
{
    $fieldsLayout->addColumn("picture", 4)
        ->addColumn("name");
}
```

With this configuration, `picture` and `name` will have a width of 4/12 and 8/12 (respectively). The third column, `capacity`, will be hidden.

### `getListData()`

Now the real work: grab and return the actual list data. This method must return an array of `instances` of our `entity`. You can do this however you want, so let's see a generic example:

The returned array is meant to be built with 2 rules:

- each item must define the keys declared in the `buildListFields()` function,

- plus one attribute for the identifier, which is `id` by default (more on that later).

So for instance, if we defined 2 columns `name` and `capacity`:

```php
function getListData()
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

In a more realistic project, you'll want to transform your data before sending it to the front code. Sharp can help with that, as explained in the detailed [How to transform data](how-to-transform-data.md) documentation.

#### Handle query params

The EntityList has a valued `$this->queryParams` property. This object will be filled by Sharp with query params:

- sorting: `$this->queryParams->sortedBy()` and `$this->queryParams->sortedDir()`

- search: `$this->queryParams->hasSearch()` and `$this->queryParams->searchWords()`

- filters: `$this->queryParams->filterFor($filter)`

If the Entity List was configured to handle sort, filters or search (see below to learn how), and if the user performed such an action, values will be accessible here.

You can use the `queryParams` everywhere except in the `buildListConfig()` function. Use cases could be, apart from filtering data: organizing columns, or maybe hiding commands...

##### Sort

`$this->queryParams->sortedBy()` contains the name of the attribute, and `$this->queryParams->sortedDir()` the direction: `asc` or `desc`.

Note that the ability of sorting a column is defined in `buildListFields()`.

##### Search

`$this->queryParams->hasSearch()` returns true if the user entered a search, and `$this->queryParams->searchWords()` returns an array of search terms. This last method can take parameters, here's its full signature:

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
if ($this->queryParams->hasSearch()) {
    foreach ($this->queryParams->searchWords() as $word) {
        $spaceships->where(function ($query) use ($word) {
            $query->orWhere('name', 'like', $word)
                ->orWhere('pilots.name', 'like', $word);
            });
        }
    }
}
```

##### Filters

We haven't seen yet how we can build a `Filter`, but at this stage, a filter is a `key` and a `value`. So we can grab this calling `$filterValue = $this->queryParams->filterFor($filterKey)`, and use the value in our query code.

#### Pagination

It's very common to return in `getListData()` paginated results:  return a `Illuminate\Contracts\Pagination\LengthAwarePaginator` in this case.

With `Eloquent` or the `QueryBuilder`, this means calling `->paginate($count)` on the query.

### `buildListConfig()`

Finally, this last function must describe the list config. Let's see an example:

```php
function buildListConfig()
{
    $this->configureInstanceIdAttribute("id")
        ->configureSearchable()
        ->configureDefaultSort("name", "asc")
        ->configurePaginated();
}
```

Here is the full list of available methods:

- `configureInstanceIdAttribute(string $instanceIdAttribute)`: define this if the id attribute of an instance is
  not `id`

- `configureReorderable(ReorderHandler|string $reorderHandler)`: allow instances to be rearranged;
  see [detailed documentation](reordering-instances.md)

- `configureSearchable()`: Sharp will display a search text input and process its content to
  fill `EntityListQueryParams $queryParams` (see above)

- `configureDefaultSort(string $sortBy, string $sortDir = "asc")`: `EntityListQueryParams $queryParams` will be filled
  with this default value (see above)

- `configurePaginated(bool $paginated = true)`: this means that `getListData()` must return an instance
  of `LengthAwarePaginator` (see above) and that Sharp will display pagination links if needed

- `configureMultiformAttribute(string $attribute)`: handle various types of entities; see [detailed doc](multiforms.md)

- `configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false)`:
  display a dynamic message above the list; [see detailed doc](page-alerts.md)

- `configureEntityState(string $stateAttribute, $stateHandlerOrClassName)`: add a state
  toggle, [see detailed doc](entity-states.md)

- `configurePrimaryEntityCommand(string $commandKeyOrClassName)`: define an instance command as "
  primary", by passing its key or full cass name. The command should be declared for this Entity
  List ([see related doc](commands.md)).

## Configure the entity list

The Entity List must be declared in the correct entity class, as documented here: [Write an entity](entity-class.md)).

After this we can access the Entity List at the following URL:
**/sharp/s-list/spaceship** (replace "spaceship" by our entity key).

To go ahead and learn how to add a link in the Sharp side menu, [look here](building-menu.md).
