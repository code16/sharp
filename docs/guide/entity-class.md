---
sidebarDepth: 3
---

# The entity class

An `entity` is simply a data structure which has a meaning in the application context. For instance, a `Person`, a `Post` or an `Order`. It's typically a Model — but it's not necessarily a 1-1 relationship, a Sharp `entity` can represent a portion of a Model, or several Models.

The `entity class` is the place where you can declare the entity configuration: its Entity List, Form, Show Page...

## Generator

```bash
php artisan sharp:make:entity <class_name> [--label,--dashboard,--show,--form,--policy,--single]
```

::: tip
The Entity name should be singular, in CamelCase and end with the "Entity" suffix. For instance: `ProductEntity`.
:::

## Write the class

The class must extend `Code16\Sharp\Entities\SharpEntity`. The easiest way to declare your attached classes is to simply override a bunch of protected attributes: 

```php
class ProductEntity extends SharpEntity
{
    protected string $label = 'Product';
    protected ?string $list = ProductList::class;
    protected ?string $show = ProductShow::class;
    protected ?string $form = ProductForm::class;
}
```

Here is the full list:
- `$list`, `$show`, `$form` and `$policy` may be set to a full classname of a corresponding type. The following sections of this documentation describe all this in detail, allowing you to build your Sharp backend.
- `string $label` is used in the breadcrumb, as a default ([see the breadcrumb documentation for more on this](sharp-breadcrumb.md)). You can simply put your entity name here.
- `bool $isSingle` must be set only if you are dealing [with a single show](single-show.md)
- and finally `array $prohibitedActions` can be used to set globally prohibited actions on the entity, [as documented here](entity-authorizations.md).

### Dashboard

Dashboard only needs to override one protected attribute: `$view`.  
Note that the class extends `SharpDashboardEntity` instead of `SharpEntity`.

```php
class SalesDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = SalesDashboard::class;
}
```

### Override methods instead

If you need more control, you can override these instead of the attributes:

```php
protected function getLabel(): string {}
protected function getList(): ?string {}
protected function getShow(): ?string {}
protected function getForm(): ?string {}
protected function getPolicy(): string|SharpEntityPolicy|null {}
```

The last one, `getPolicy()`, allows you to return a `SharpEntityPolicy` implementation instead of a classname, as it's sometimes easier to declare a quick policy right in here. For example:

```php
class MyEntity extends SharpEntity
{
    // ...

    protected function getPolicy(): string|SharpEntityPolicy|null
    {
        return new class extends SharpEntityPolicy
        {
            public function update($user, $instanceId): bool
            {
                return $user->isBoss();
            }
        };
    }
}
```

### Single shows and forms

When you need to configure a "unique" resource that does not fit into a List / Show schema, like an account or a configuration item, you can use a Single Show or Form. This is a dedicated topic, [documented here](single-show.md).

### Handle Multiforms

::: info
This feature has been deprecated and was replaced in version 9.6.0 by the [Entity Map](./building-entity-list.md#entity-map) feature.
:::

## Declare the Entity in Sharp configuration

The last step is to declare the entity in Sharp, in your `SharpAppServiceProvider` implementation. 

### Autodiscovery

The easiest way is to let Sharp discover your entities:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->discoverEntities();
            // ...
    }
}
```

The `discoverEntities()` method will scan the `app_path('Sharp/Entities')` directory for all Entity classes, and declare them in Sharp. You can pass an array of other paths to scan if needed:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->discoverEntities([__DIR__ . '../Domain/OtherEntities']);
            // ...
    }
}
```

Each Entity is keyed by and entity key, used everywhere in Sharp (starting with the URL).
When using autodiscovery, the entity key is automatically set to the class name, in kebab-case. For instance, `ProductEntity` will have the entity key `product`.

### Choosing your own entity key

If for whatever reason you want to choose your own entity key, you can set it in the entity class:

```php
class ProductEntity extends SharpEntity
{
    public static string $entityKey = 'my-product';
    // ...
}
```

### Manual declaration

If you want to have control over the entity declaration, you can declare them manually instead of using discovery:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->declareEntity(ProductEntity::class);
            // ...
    }
}
```

### Custom Entity Resolver

In some very specific cases, you may want to have full control over the entity declaration, depending on some context. You can use a custom `SharpEntityResolver` to do that.

```php
use Code16\Sharp\Utils\Entities\SharpEntityResolver;

class MySharpEntityResolver implements SharpEntityResolver
{
    public function entityClassName(string $entityKey): ?string
    {
        return match ($entityKey) {
            'product' => auth()->user()->isAdmin() 
                ? AdminProductEntity::class
                : ProductEntity::class,
            'order' => OrderEntity::class,
            // ...
        };
    }
}
```

Then, in the ServiceProvider, you can declare the resolver like this:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->declareEntityResolver(MySharpEntityResolver::class);
            // ...
    }
}
```

::: warning
You must remove all `->declareEntity()` calls in order to use `->declareEntityResolver()`.
:::

::: warning
If you are using a custom entity resolver, you won’t be able to use the `SharpEntity` classes in the [menu](building-menu.md), or in [`LinkTo` links](link-to.md), or for [embedded entity lists](show-fields/embedded-entity-list.md): you will have to use the entity key instead. For instance: `LinkToForm::make('products', $id)`.
:::
