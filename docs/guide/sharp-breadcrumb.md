# Sharp's breadcrumb

Under the hood Sharp manages a breadcrumb to keep track of stacked pages.

## Configure entity label

In Entity classes, you can define how an entity should be labeled in the breadcrumb with the `label` attribute:

```php
class PostEntity extends \Code16\Sharp\Utils\Entities\SharpEntity
{
    // [...]
    
    protected string $label = = 'Post';
}
```

## Customize the label on an instance

In the Form and in the Show Page, you can define which attribute should be used as the breadcrumb label, if you need to be specific.

```php
class PostShow extends \Code16\Sharp\Show\SharpShow
{
    // [...]
    
    function buildShowConfig(): void
    {
        $this->configureBreadcrumbCustomLabelAttribute('title');
    }
}
```

As any attribute, you can use a dedicated custom transformer to valuate it as you want:

```php
class PostShow extends \Code16\Sharp\Show\SharpShow
{
    // [...]
    
    function buildShowConfig(): void
    {
        $this->configureBreadcrumbCustomLabelAttribute('breadcrumb_label');
    }
    
    function find($id): array
    {
        return $this
            ->setCustomTransformer('breadcrumb_label', function($role, $post) {
                return str($post->title)->limit(20);
            })
            ->transform(Post::findOrFail($id));
    }
}
```

::: tip
In the Form, the breadcrumb label is only used in one particular case: when coming from an embedded Entity List inside a Show Page. In this case, the Show Page and the Form entity are different, and the breadcrumb helps to keep track of the current edited entity.
:::

## Configure custom labels cache

Breadcrumb labels are cached for 30 minutes to reduce DB queries between each navigation. If you don't want to cache them, which means all `SharpShow` in breadcrumb are loaded on every navigation, you can update the config in the SharpServiceProvider:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->configureBreadcrumbLabelsCache(false)
            // ...
    }
}
```

Alternatively, you can change the cache duration (default is 30 minutes):

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->configureBreadcrumbLabelsCache(duration: 10)
            // ...
    }
}
```

### Lazy loading

In some cases, having the labels replaced by the default Entity label is acceptable and you want to have less DB queries, you can activate the lazy loading:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->enableBreadcrumbLabelsLazyLoading()
    }
}
```
::: warning
Be aware that the user may see the breadcrumb with default entity labels (e.g. "Posts > Post > Category > Edit")  when :
- a nested page is accessed directly (e.g. direct link)
- cached labels are expired
:::

## Hide the breadcrumb

If you don't want any breadcrumb, you can hide it in sharp's configuration:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->displayBreadcrumb(false)
            // [...]
    }
}
```

## Interact with Sharp's Breadcrumb

Refer to [the Context documentation](context.md) to find out how to interact with Sharp's breadcrumb.
