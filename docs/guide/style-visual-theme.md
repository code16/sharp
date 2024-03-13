# Style & Visual Theme

### Custom colors

The primary color is customisable, and is applied to the header and buttons. Although every hue works well, too light colors aren't supported (e.g. works well with [tailwind colors](https://tailwindcss.com/docs/customizing-colors#color-palette-reference) >= 600).

```php
// config/sharp.php
return [
    // [...]
    
    'theme' => [
        'primary_color' => '#004D40',
    ],
];
```

### Header logo

By default, the `config('sharp.name')` is displayed on the header. If you want to show custom logo, you can do it with this config:

```php
// config/sharp.php

return [
    // [...]
    
    'theme' => [
        'logo_url' => '/my-sharp-assets/my-custom-menu-icon.png',
        // [...]
    ],
];
```

The file should be an SVG or PNG file, and must fit in 150 pixels in width and 50 pixels in height.

### Login form

You can customize the login form with a custom logo and a custom message.

```php
// config/sharp.php

return [
    // [...]
    
    'auth' => [
        // [...]
        
        'login_form' => [
            // [...]
            'display_app_name' => true,
            'logo_url' => '/my-sharp-assets/login-logo.png',
            'message_blade_path' => 'sharp/login-page-message',
        ],
    ],
];
```

The `display_app_name` option allows you to display the `config('sharp.name')` on the login form.

`logo_url` should point to an SVG or PNG file, which must fit in 200 pixels in width and 100 pixels in height. If not set, the `theme.logo_url` file will be displayed, or the Sharp logo as a last resort.

The custom message is displayed under the form; you'll need to create a new template file:

```blade
<!-- resources/views/sharp/login-page-message.blade.php -->

<div class="alert alert-info">
    Display a custom message to your users
</div>
```

### Favicon

You can define an URL for a favicon that Sharp will use in the config:

```php
// config/sharp.php

return [
    // [...]
    
    'theme' => [
        'favicon_url' => '/sharp-img/favicon.png',
    ],
]
```

### Injecting Assets

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
