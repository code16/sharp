---
sidebarDepth: 3
---

# Create an Entity List

We need an Entity List to display the list of `instances` for an `entity`. This list can be paginated, searchable, filtered, ... as we'll see below.

## Generator

```bash
php artisan sharp:make:entity-list <class_name> [--model=<model_name>]
```

::: tip
The Entity List name should be singular, in CamelCase and must end with the "List" suffix. For instance: `ProductList`.
:::

## Write the class

First let's write the applicative class, and make it extend `Code16\Sharp\EntityList\SharpEntityList`. Therefore, there are two methods to implement:
- `buildList(EntityListFieldsContainer $fields)` for the structure,
- and `getListData()` for the actual data of the list.

There are a two more optional methods, for the list config and instance deletion. 
Each one is detailed here:

### `buildList(EntityListFieldsContainer $fields)`

A field is a column in the `Entity List`. This first function is responsible to describe each column:

```php
class ProductList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Full name')
                    ->setSortable()
                    ->setWidth('50%')
                    ->setHtml()
            )
            ->addField(/* ... */);
    }
    // [...]
}
```

Setting the label, allowing the column to be sortable and to display html is optional.

The optional `->setWidth()` method accepts either an integer (eg: `20` for 20%), a float (eg: `.2` for 20%) or a string (eg: `'20'` or `'20%'`); if missing, it will be deduced (you can use `->setWidthFill()` to force this last behavior).
To hide the column on small screens, use `->hideOnSmallScreens()`.

Sorting columns must be handled in the `getListData()` method, see below.

### `getListData()`

Now the real work: grab and return the actual list data. This method must return an array of `instances` of our `entity`. You can do this however you want, so let's see a generic example:

The returned array is meant to be built with 2 rules:
- each item must define the keys declared in the `buildList()` function,
- plus one attribute for the identifier, which is `id` by default (more on that later).

So for instance, if we defined 2 columns `name` and `price`:

```php
class ProductList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        return [
            [
                'id' => 1,
                'name' => 'Carrot',
                'price' => '0.5'
            ], [
                'id' => 2,
                'name' => 'Potato',
                'price' => '0.95'
            ]
        ];
    }
    // [...]
}
```

Of course, real code would imply some data request in a DB, or a file for instance; the important thing is that Sharp don’t care.

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

Note that the ability of sorting a column is defined in `buildList()`.

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
class ProductList extends SharpEntityList
{
    public function getListData(): array|Arrayable
    {
        $products = Product::query();
        
        if ($this->queryParams->hasSearch()) {
            foreach ($this->queryParams->searchWords() as $word) {
                $products->where(fn ($query) => $query
                    ->orWhere('name', 'like', $word)
                    ->orWhere('reference', 'like', $word)
                );
            }
        }
        
        return $this->transform($products->paginate(50));
    }
    
    // ...
}
```

##### Filters

A filter is referenced by a `filterKey` and has a `value`. So we can grab this calling `$filterValue = $this->queryParams->filterFor($filterKey)`, and use the value in our query code.

#### Pagination

It's very common to return in `getListData()` paginated results: return a `Illuminate\Contracts\Pagination\LengthAwarePaginator` or a `Illuminate\Contracts\Pagination\Paginator` in this case.

With `Eloquent` or the `QueryBuilder`, this means calling `->paginate($count)` or `simplePaginate($count)` on the query.

### `delete($id): void`

Here you might write the code performed on a deletion of the instance. It can be anything, here’s an Eloquent example:

```php
class ProductList extends SharpEntityList
{
    function delete($id): void
    {
        Product::findOrFail($id)->delete();
    }
    
    // ...
}
```

Deletion is typically an action you perform [in a Show Page](building-show-page.md), but it is also available in the Entity List for convenience. You can configure this action to hide it, and of course leverage specific authorizations (more on this below). 

### `buildListConfig()`

Finally, this last function must describe the list config. Let's see an example:

```php
class ProductList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureInstanceIdAttribute('id')
            ->configureSearchable()
            ->configureDefaultSort('name', 'asc');
    }
    
    // ...
}
```

Here is the full list of available methods:

- `configureInstanceIdAttribute(string $instanceIdAttribute)`: define this if the id attribute of an instance is not `id`

- `configureReorderable(ReorderHandler|string $reorderHandler)`: allow instances to be rearranged; see [detailed documentation](reordering-instances.md)

- `configureSearchable()`: Sharp will display a search text input and process its content to fill `EntityListQueryParams $queryParams` (see above)

- `configureDefaultSort(string $sortBy, string $sortDir = "asc")`: `EntityListQueryParams $queryParams` will be filled with this default value (see above)

- `configureMultiformAttribute(string $attribute)`: handle various types of entities; see [detailed doc](multiforms.md)

- `configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false)`: display a dynamic message above the list; [see detailed doc](page-alerts.md)

- `configureEntityState(string $stateAttribute, $stateHandlerOrClassName)`: add a state toggle, [see detailed doc](entity-states.md)

- `configurePrimaryEntityCommand(string $commandKeyOrClassName)`: define an instance command as "primary", by passing its key or full cass name. The command should be declared for this Entity List ([see related doc](commands.md)).

- `configureQuickCreationForm(?array $fields = null)`: show the creation form in a modal instead of a full page ([see detailed doc](quick-creation-form.md))

- `configureDelete(bool $hide = false, ?string $onfirmationText = null)`: the first argument is to show / hide the delete command on each instance (shown by default); this is only useful to hide the link if you want to only display the delete action in the Show Page (if you have defined one), this is NOT to be used for authorization purpose (see [dedicated documentation on this topic](entity-authorizations.md)). The second argument is the message to display in the confirmation dialog (a sensible default will be used).

- `configureCreateButtonLabel(string $label)` to set a custom "New..." button label.

## Configure the Entity List

The Entity List must be declared in the correct entity class, as documented here: [Write an entity](entity-class.md)).

After this we can access the Entity List at the following URL: **/sharp/s-list/products** (replace "products" by our entity key).

To go ahead and learn how to add a link in the Sharp side menu, [look here](building-menu.md).
