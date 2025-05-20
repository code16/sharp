# Create the main menu

The Sharp UI is organized with two menus: the main one on a left sidebar, and the user menu is a dropdown located in the bottom left corner.

All links shares common things: an icon, a label and an URL. Links can be grouped in categories.

## Create a SharpMenu class

### Generator

```bash
php artisan sharp:make:menu <class_name>
```

### Write and declare the class

The class must extend `Code16\Sharp\Utils\Menu\SharpMenu`, and define a required `build()` method:

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        // ...
    }
}
```

And should be declared in your SharpServiceProvider:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setSharpMenu(MySharpMenu::class)
            // ...
    }
}
```

::: info
The `SharpServiceProvider` class is created bye the `sharp:install` artisan command; in case you don't have it, you can create it by yourself in the `App\Providers` namespace, or use the `sharp:make:provider` command.
:::

### Link to an Entity List, a Dashboard or to a single Show Page

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->addEntityLink(PostEntity::class, 'Posts')
            ->addEntityLink(CategoryEntity::class, 'Categories');
    }
}
```

In this example, `PostEntity::class` and `CategoryEntity::class` should be `SharpEntity` classes declared in Sharp’s configuration. Sharp will create a link either to the Entity List, to the Dashboard or to a [single Show Page](single-show.md) (depending on the entity configuration).

### Link to an external URL

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this->addExternalLink('https://google.com', 'Some external link');
    }
}
```

### Define icons

Yon can specify a [blade-icons](https://blade-ui-kit.com/blade-icons) name for each link. It can be an icon set coming from a [package](https://github.com/blade-ui-kit/blade-icons?tab=readme-ov-file#icon-packages) or defined in the project config.

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->addEntityLink(PostEntity::class, 'Posts', icon: 'fas-file')
            ->addEntityLink(DirectoryEntity::class, 'Directories', icon: 'heroicon-o-folder')
            ->addExternalLink('https://example.org', 'Homepage', icon: 'icon-logo'); // icon defined in the project (e.g. in resources/svg)
    }
}
```

### Group links in sections

Sections are groups that can be collapsed

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->addSection('Admin', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink(AccountEntity::class, 'My account')
                    ->addEntityLink(UserEntity::class, 'Sharp users');
            });
    }
}
```

### Add separators in sections

You can add a simple labelled separator in sections:

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->addSection('Admin', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink(AccountEntity::class, 'My account')
                    ->addSeparator('Other users')
                    ->addEntityLink(UserEntity::class, 'Sharp users');
            });
    }
}
```

### Set a section to be non-collapsible

A section is collapsible by default, but you may want to always show it to the user

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->addSection('Admin', function (SharpMenuItemSection $section) {
                $section
                    ->setCollapsible(false)
                    ->addEntityLink(AccountEntity::class, 'My account');
            });
    }
}
```

### Hide the menu

If for some reason you want to hide the menu, you can do that with the `setVisible()` method:

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->setVisible(auth()->user()->isAdmin())
            ->addSection(/*...*/);
    }
}
```

### Add links in the user (profile) menu

Next to user's name or email, Sharp displays a dropdown menu with a logout link. You can add your own links in this menu:

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this
            ->setUserMenu(function (SharpMenuUserMenu $menu) {
                $menu->addEntityLink(AccountEntity::class, 'My account');
            });
    }
}
```

### Global menu Filters

If you want to display a filter on all pages, above the menu, useful to scope the entire data set (use cases: multi tenant app, customer selector...), you can define a global filter as described in the [Filters documentation](filters.md#global-menu-filters).