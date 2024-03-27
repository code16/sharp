<?php

return [
    'name' => 'Demo project',
    'custom_url_segment' => 'sharp',
    'display_sharp_version_in_title' => true,
    'display_breadcrumb' => true,
    'entities' => [
        'posts' => \App\Sharp\Entities\PostEntity::class,
        'blocks' => \App\Sharp\Entities\PostBlockEntity::class,
        'categories' => \App\Sharp\Entities\CategoryEntity::class,
        'authors' => \App\Sharp\Entities\AuthorEntity::class,
        'profile' => \App\Sharp\Entities\ProfileEntity::class,
        'dashboard' => \App\Sharp\Entities\DemoDashboardEntity::class,

        'test' => \App\Sharp\Entities\TestEntity::class,
    ],

    'global_filters' => fn () => auth()->id() === 1 ? [] : [\App\Sharp\DummyGlobalFilter::class],

    'search' => [
        'enabled' => true,
        'placeholder' => 'Search for posts or authors...',
        'engine' => \App\Sharp\AppSearchEngine::class,
    ],

    'menu' => \App\Sharp\SharpMenu::class,

    'uploads' => [
        'tmp_dir' => env('SHARP_UPLOADS_TMP_DIR', 'tmp'),
        'thumbnails_disk' => env('SHARP_UPLOADS_THUMBS_DISK', 'public'),
        'thumbnails_dir' => env('SHARP_UPLOADS_THUMBS_DIR', 'thumbnails'),
        'transform_keep_original_image' => true,
        'model_class' => \App\Models\Media::class,
    ],

    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'suggest_remember_me' => false,
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 5,
        ],
        '2fa' => [
            'enabled' => true,
            'handler' => env('DEMO_2FA_TOTP_ENABLED', false)
                ? 'totp'
                : \App\Sharp\Demo2faNotificationHandler::class,
        ],
        'forgotten_password' => [
            'enabled' => true,
            // 'password_broker' => null,
            //  'reset_password_callback' => null,
        ],
        'display_attribute' => 'name',
        'impersonate' => [
            'enabled' => env('SHARP_IMPERSONATE', false),
            'handler' => null,
        ],
        'login_form' => [
            // Handle a "remember me" flag (with a checkbox on the login form)
            'suggest_remember_me' => false,

            // Display the app name on the login page.
            'display_app_name' => true,

            // Optional logo on the login page (default to theme.logo_url and to sharp logo)
            // 'logo_url' => '/img/sharp/login-icon.png',

            // Optional additional message on the login page.
            'message_blade_path' => 'sharp/_login-page-message',
        ],

        // "check_handler" => \App\Sharp\Auth\MySharpCheckHandler::class,
    ],

    'theme' => [
        'primary_color' => '#0c4589',
        'favicon_url' => '/img/sharp/favicon-32x32.png',
        'logo_url' => '/img/sharp/logo.png',
    ],

    'extensions' => [
        'assets' => [
            'strategy' => 'vite',
            'head' => [
                'resources/css/sharp-extension.css',
            ],
        ],
        'activate_custom_fields' => true,
    ],

    'markdown_editor' => [
        'nl2br' => false,
        'tight_lists_only' => true,
    ],
];
