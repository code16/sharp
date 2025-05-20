# Style & Visual Theme

### Custom colors

The primary color is customisable, and is applied to the header and buttons. Although every hue works well, too light colors aren't supported (e.g. works well with [tailwind colors](https://tailwindcss.com/docs/customizing-colors#color-palette-reference) >= 600).

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setThemeColor('#004D40')
            // [...]
    }
}
```

### Header logo

By default, the configured `name` is displayed on the header. If you want to show custom logo, you can do it with this config:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setName('My Sharp App')
            ->setThemeLogo(
                logoUrl: '/my-sharp-assets/my-custom-logo.svg',
                logoHeight: '1.5rem',
                faviconUrl: '/my-sharp-assets/favicon.png'
            )
            // [...]
    }
}
```

The file should be an SVG, you can customize the logo height by setting the `logo_height` config.

:::tip
With the newly added dark theme, it is recommended to use an SVG logo with `fill="currentColor"` to allow the logo to adapt to the theme, Sharp will handle it for you.
:::

### Login form

You can customize the login form with a custom message.

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->appendMessageOnLoginForm('sharp.login-page-message')
            // or a direct message
            // ->appendMessageOnLoginForm('Display a custom message to your users')
            // [...]
    }
}
```

The custom message is displayed under the form; you can either provide HTML or the name of a custom blade template file.

```blade
<!-- resources/views/sharp/login-page-message.blade.php -->

<x-sharp::card>
    Display a custom message to your users
</x-sharp::card>
```

### Favicon

You can define an URL for a favicon that Sharp will as a 3rd argument of the same `setThemeLogo()` method:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->setThemeLogo(
                faviconUrl: '/my-sharp-assets/favicon.png'
            )
            // [...]
    }
}
```

### Injecting CSS

If you want to inject custom CSS in Sharp, you can do so by using `loadViteAssets()` or `loadStaticCss()`. Be aware that tailwind classes may clash with Sharp default CSS so you may define a [Tailwind prefix](https://tailwindcss.com/docs/configuration#prefix).

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->loadViteAssets(['resources/css/sharp.css']) // to load a CSS file built with Vite
            ->loadStaticCss(asset('/css/sharp.css')) // Or to load a static CSS file
    }
}
```
