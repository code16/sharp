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

### Injecting Assets

TODO check this documentation

You may globally inject custom CSS files after the Sharp assets by defining their paths in the `config/sharp.php` config file.

```php
// config/sharp.php

return [
    // [...]

    'extensions' => [
       'assets' => [
          'strategy' => 'raw',
          'head' => [
             '/css/inject.css', // Outputs <link rel="stylesheet" href="/css/inject.css"> after sharp assets
          ],
       ],
    ],
];
```

The comment next to the item within the `head` position show how the output would appear in the HTML.

### Strategy

The `strategy` defines how the asset path will be rendered

- `raw` to output the path in the form it appears in your array
- `asset` to pass the path to the laravel [`asset()`](https://laravel.com/docs/5.6/helpers#method-asset) function
- `mix` to pass the path to the laravel [`mix()`](https://laravel.com/docs/5.6/helpers#method-mix) function
- `vite` to pass to path to the laravel [`Vite::asset()`](https://laravel.com/docs/10.x/vite#blade-processing-static-assets) function
