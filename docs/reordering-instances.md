# Reordering instances

It's sometimes useful to allow the user to rearrange instances right from the Entity List. Let's say we want to let the user choose some `pages` order:

## Write the class

First, we need to write a class for the reordering itself, which must implement `Code16\Sharp\EntityList\Commands\ReorderHandler`, and therefore the `reorder(array $ids)` function. 

Here's an example with Eloquent and a numerical `order` column:

```php
    class PageReorderHandler implements ReorderHandler
    {
    
        function reorder(array $ids)
        {
            $pages = Page::whereIn("id", $ids)->get();
    
            foreach($pages as $page) {
                $page->order = array_search($page->id, $ids) + 1;
                $page->save();
            }
        }
    }
```

## Configure reorder for the front-end

Then, in the `SharpEntityList` class, we have to configure our reorder handler:

```php
    function buildListConfig()
    {
        $this->setReorderable(new PageReorderHandler());
    }
```

And that's it! The list now presents a "Reorder" button, and your code will be called when needed.

## Handle exceptions

If you need to abort the process, for any reason, you can raise a `Code16\Sharp\Exceptions\SharpException\SharpApplicativeException` in the `reorder(array $ids)` function.