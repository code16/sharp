# Building the menu

The Sharp UI is organized with two menus: the main one is on a left sidebar, and the user menu is a dropdown located in the top right corner.

![](./img/menu-v8.png)

All links shares common things: an icon, a label and an URL.

Links can be grouped in categories, like "Blog" in this screenshot.

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
            // [...]
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
            ->addEntityLink('post', 'Posts', 'fas fa-file')
            ->addEntityLink('category', 'Categories', 'fas fa-folder');
    }
}
```

In this example, "post" and "category" should be entities defined in the config file. Sharp will create a link either to the Entity List, to the Dashboard or to a [single Show Page](single-show.md) (depending on the entity configuration).

### Link to an external URL

```php
class MySharpMenu extends Code16\Sharp\Utils\Menu\SharpMenu
{
    public function build(): self
    {
        return $this->addExternalLink('https://google.com', 'Some external link', 'fas fa-globe');
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
            ->addSection('Admin', function(SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('account', 'My account', 'fas fa-user')
                    ->addEntityLink('user', 'Sharp users', 'fas fa-user-secret');
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
            ->addSection('Admin', function(SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('account', 'My account', 'fas fa-user')
                    ->addSeparator('Other users')
                    ->addEntityLink('user', 'Sharp users', 'fas fa-user-secret');
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
            ->addSection('Admin', function(SharpMenuItemSection $section) {
                $section
                    ->setCollapsible(false)
                    ->addEntityLink('account', 'My account', 'fas fa-user');
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
            ->addSection(...);
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
                $menu->addEntityLink('account', 'My account', 'fas fa-user');
            });
    }
}
```
