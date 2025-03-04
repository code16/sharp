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

When you need to configure a "unique" resource that does not fit into a List / Show schema, like for instance an account or a configuration item, you can use a Single Show or Form. This is a dedicated topic, [documented here](single-show.md).

### Handle Multiforms

Multiforms allows to declare different forms for the same entity, to hanle variants. This is a dedicated topic, [documented here](multiforms.md).

## Declare the Entity in Sharp configuration

The last step is to declare the entity in Sharp, in your `SharpAppServiceProvider` implementation. 

### Autodiscovery

The easiest way is to let Sharp autodiscover your entities:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->autodiscoverEntities('Sharp/Entities');
            // ...
    }
}
```

The `autodiscoverEntities()` method will scan the given directory (path relative to `app_path()`) for all Entity classes, and declare them in Sharp. There are a few catches though:
- The entity key will be the class name, minus the Entity suffix, in kebab-case. For instance, `BestProductEntity` will be declared as `best-product`.
- Each php file must correspond to one class, named after the file name (this should always be the case if you follow PSR-4).

::: note
A note on entity keys: Sharp is using the entity key everywhere internally (starting in the URL). The only rule here is to have a unique key for each entity, which should always be the case with the autodiscovery mechanism (apart in tricky cases like one `ProductEntity` and another `ProductsEntity`: don’t do that).
:::

### Manual declaration

If you want to have control over the entity declaration, you can instead declare them manually:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->addEntity('products', ProductEntity::class);
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
You must remove all `->addEntity()` calls in order to use `->declareEntityResolver()`.
:::

::: warning
If you are using a custom entity resolver, you won’t be able to use the `SharpEntity` classes in the [menu](building-menu.md), or in [`LinkTo` links](link-to.md), or for [embedded entity lists](show-fields/embedded-entity-list.md): you will have to use the entity key instead. For instance: `LinkToForm::make('products', $id)`.
:::