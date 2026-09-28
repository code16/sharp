# Installation

Sharp 9 needs Laravel 11+ and PHP 8.3+.

- Add the package with composer: `composer require code16/sharp`
- Then run: `php artisan sharp:install`

This last script will publish required assets, create a `SharpServiceProvider` in the `App\Providers` namespace and a `SharpMenu` class in the `App\Sharp` namespace.

## Configuration via a new Service Provider

All Sharp behavior is configured in the `App\Providers\SharpServiceProvider` class created by the `sharp:install` command; you can declare your entities in the `configureSharp()` method:

```php
use Code16\Sharp\SharpAppServiceProvider;
use Code16\Sharp\Config\SharpConfigBuilder;
use App\Sharp\SharpMenu;

class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My new project')
            ->setSharpMenu(SharpMenu::class)
            ->declareEntity(ProductEntity::class);
            // ...
    }
}
```

::: tip
As shown in the [Entity class](entity-class.md) documentation, you can also let Sharp auto-discover your entities.
:::

This `ProductEntity` class could be written like this:

```php
class ProductEntity extends SharpEntity
{
    protected string $label = 'Product';
    protected ?string $list = ProductList::class;
    protected ?string $show = ProductShow::class;
    protected ?string $form = ProductForm::class;
    protected ?string $policy = ProductPolicy::class;
}
```

We chose to define:

- a `list` class, responsible for the `Entity List`,
- a `show` class, responsible for displaying an `instance` in a `Show Page`,
- a `form` class, responsible for the creation and edit `Form`,
- and a `policy` class, for authorizations.

Almost each one is optional: we could skip the `show` and go straight to the `form` from the `list`, for instance. 

We'll get into all those classes in this guide. The important thing to notice is that Sharp provides base classes to handle all the wiring (and more), but as we'll see, the applicative code is totally up to you.

::: tip
Use the artisan command `php artisan sharp:make:entity` to generate a new entity with all the required classes, or the global one (prompt based) `php artisan sharp:generator`.
:::

## Access to Sharp

Once installed, Sharp is accessible via the url `/sharp`, by default. If you wish to change this default value, you'll need to configure a custom segment path:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setCustomUrlSegment('admin')
            // ...
    }
}
```
