# Reordering instances

It's sometimes useful to allow the user to rearrange instances right from the Entity List. Let's say we want to let the user choose some `pages` order:

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
        Page::whereIn("id", $ids)
            ->get()
            ->each(function(Page $page) use ($ids) {
                $page->order = array_search($page->id, $ids) + 1;
                $page->save();
            });
    }
}
```

## Configure reorder for the front-end

Then, in the `SharpEntityList` class, we have to configure our reorder handler:

```php
public function buildListConfig()
{
    $this->configureReorderable(new PageReorderHandler());
}
```

And that's it, the list now presents a "Reorder" button, and your code will be called when needed.

## Handle exceptions

If you need to abort the process, for any reason, you can raise a `Code16\Sharp\Exceptions\SharpException\SharpApplicativeException` in the `reorder(array $ids)` function.

## Use the default Eloquent implementation

A common pattern, with an Eloquent model, is to simply define an `order` attribute. In this simple case, you can leverage a default implementation built in Sharp:

```php
public function buildListConfig()
{
    $this->configureReorderable(new SimpleEloquentReorderHandler(MyModel::class));
}
```

The `Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler` class expects the full classname of the Eloquent Model to reorder, and will use the `id` and `order` attribute by default. You can change this default behavior with the dedicated methods:

```php
public function buildListConfig()
{
    $this->configureReorderable(
        new SimpleEloquentReorderHandler(MyModel::class)
            ->setIdAttribute('uuid')
            ->setOrderAttribute('position')
    );
}
```
