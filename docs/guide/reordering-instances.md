# Reordering instances

Allow the user to rearrange instances in the Entity List.

## Generator Command

```bash
php artisan sharp:make:reorder-handler <class_name> [--model=<model_name>]
```

## Write the class

First, we need to write a class for the reordering itself, which must implement `Code16\Sharp\EntityList\Commands\ReorderHandler`, and therefore the `reorder(array $ids)` function.

Here's an example with Eloquent and a numerical `order` column:

```php
class PageReorderHandler implements ReorderHandler
{
    function reorder(array $ids)
    {
        Page::whereIn('id', $ids)
            ->get()
            ->each(function (Page $page) use ($ids) {
                $page->order = array_search($page->id, $ids) + 1;
                $page->save();
            });
    }
}
```

## Configure reorder for the front-end

Then, in your Entity List you have to configure your reorder handler:

```php
class PageEntityList extends SharpEntityList
{
    // [...]
    
    public function buildListConfig()
    {
        $this->configureReorderable(new PageReorderHandler());
    }
}
```

And that’s it, the list now presents a "Reorder" button, and your code will be called when needed.

::: tip
Note that you can also pass a ReorderHandler classname, or an anonymous class that extends ReorderHandler, to the `configureReorderable()` method. 
:::

## Authorizations

The reorder action depends on the `reorder` permission. You can define it in the [Entity Policy](entity-authorizations.md):

Sometimes you may need to restrict the reorder action depending on the actual data, or on some filters values. This can be achieved by using the `forbidReorder()` method in your EntityList class, typically in the `getListData()` method.

```php
class PostEntityList extends SharpEntityList
{
    // ...
    
    public function buildListConfig()
    {
        $this->configureReorderable(new PostReorderHandler());
    }
    
    public function getListData(): array|Arrayable
    {
        // We can’t reorder if there is a search 
        $this->forbidReorder($this->queryParams->hasSearch());
        
        // ...
    }
}
```

## Handle exceptions

If you need to abort the process, for any reason, you can raise a `Code16\Sharp\Exceptions\SharpException\SharpApplicativeException` in the `reorder(array $ids)` function.

## Use the default Eloquent implementation

A common pattern with an Eloquent model is to simply define an `order` attribute. In this simple case, you can leverage a default implementation built in Sharp:

```php
class PageEntityList extends SharpEntityList
{
    // [...]
 
    public function buildListConfig()
    {
        $this->configureReorderable(new SimpleEloquentReorderHandler(MyModel::class));
    }
}
```

The `Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler` class expects the full classname of the Eloquent Model to reorder, and will use the `id` and `order` attribute by default. You can change this default behavior with the dedicated methods:

```php
class PageEntityList extends SharpEntityList
{
    // [...]
 
    public function buildListConfig()
    {
        $this->configureReorderable(
            new SimpleEloquentReorderHandler(MyModel::class)
                ->setIdAttribute('uuid')
                ->setOrderAttribute('position')
        );
    }
}
```
