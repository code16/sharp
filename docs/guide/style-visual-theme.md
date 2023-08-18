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

### Login and menu logos

By default, the `config('sharp.name')` is displayed on the login page and on top of the menu. You can if you wish display images instead: Sharp will look for PNGs named `login-icon.png` and `menu-icon.png`, in a `/public/sharp-assets/` directory. Note that :
- `login-icon.png` is limited to 200 pixels in width and 100 pixels in height,
- and `menu-icon.png` must fit in 150 pixels in width and 50 pixels in height.

If you need to configure the image files URLs, you can do it with this config:

```php
// config/sharp.php

return [
    // [...]
    
    'theme' => [
        'primary_color' => ...,
        'logo_urls' => [
            'menu' => '/sharp/subdir/my-custom-menu-icon.png',
            'login' => '/sharp/subdir/my-custom-login-icon.png',
        ],
    ],
];
```

#### Display a custom message on login page

You can display a custom content under the form on login page; you'll need to create a new template file:

```blade
<!-- resources/views/sharp/_login-page-message.blade.php -->

<div class="alert alert-info">
    Display a custom message to your users
</div>
```

And then define the path to this custom blade in the `config/sharp.php` config file:

```php
// config/sharp.php

return [
    // [...]

    'login_page_message_blade_path' => 'sharp/_login-page-message',
];
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
