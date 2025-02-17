# Avoid n+1 queries in Entity Lists

How can we leverage the built-in cache to avoid to query the same instance multiple times per request?  

## The problem

Every row of an Entity List is an instance (typically a Model) which can define multiple Instance Commands and be constrained by a Policy; and Sharp will run the Policy for every instance (to check if the use is allowed to view, update and delete it), and even worse: execute the authorization logic for every Instance Command times every instance.

Let’s consider this simple example: a Post entity with a `PostPolicy` and a `PreviewPostCommand`:

```php
class PostList extends SharpEntityList
{
    public function getInstanceCommands(): ?array
    {
        return [
            PreviewPostCommand::class,
        ];
    }

    public function getListData(): array|Arrayable
    {
        $posts = Post::select('posts.*')
            ->with('author', 'categories', 'cover') // don't forget to eager load used relations
            ->paginate(20);

        return $this->transform($posts);
    }
    
    // ...
}
```

```php
class PostPolicy extends SharpEntityPolicy
{
    public function update($user, $instanceId): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
         return Post::find($instanceId)->author_id === auth()->id();
    }
}
```

```php
class PreviewPostCommand extends InstanceCommand
{
    public function authorizeFor(mixed $instanceId): bool
    {
        if (auth()->user()->isAdmin()) {
            return true;
        }

        return Post::find($instanceId)->isOnline();
    }
    
    // ...
}
```

Nothing fancy here: if we are an admin, we can update and preview any post; if we are not, we can only preview online posts and update our posts.

Notice that this is written in a way which prevents any query to be executed in the "admin" case, which is a good thing for performance; but it can hide the fact that all non-admins will face a different case: they will query the same instance twice per row + one time for the list query itself.


## Leveraging Sharp’s instances list cache to avoid this

Here’s how we can rewrite the `PostPolicy` to avoid this:

```php
class PostPolicy extends SharpEntityPolicy
{
    public function update($user, $instanceId): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        return sharp()->context()
            ->findListInstance($postId, fn($postId) => Post::find($postId))
            ->author_id === auth()->id();
    }
}
```

The same should be done for the `PreviewPostCommand`.

This `findListInstance()` method in the `sharp()->context()` helper class (see [context documentation](context.md)) will retrieve the instance from a cache that was automatically set by Sharp when calling `$this->transform($posts)`, in `PostList::getListData()`. 

The `findListInstance()` takes a second argument: this is a callback that will be called only if the instance is not already in the cache, passing the instance id as parameter. This Closure must return the instance.

::: info
Note that the cache set is automatic if you use the standard `->transform()` method. In case you don’t, you can still set it manually calling `sharp()->context()->cacheInstances(?Collection $instances)`. 
:::

With this quite simple trick you can avoid a lot of useless queries and improve the performance of your Entity Lists.