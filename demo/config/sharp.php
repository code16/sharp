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

    'menu' => \App\Sharp\SharpMenu::class,

    'uploads' => [
        'tmp_dir' => env('SHARP_UPLOADS_TMP_DIR', 'tmp'),
        'thumbnails_disk' => env('SHARP_UPLOADS_THUMBS_DISK', 'public'),
        'thumbnails_dir' => env('SHARP_UPLOADS_THUMBS_DIR', 'thumbnails'),
        'transform_keep_original_image' => true,
    ],

    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'suggest_remember_me' => false,
        'display_attribute' => 'name',
        // "check_handler" => \App\Sharp\Auth\MySharpCheckHandler::class,
    ],

    'theme' => [
        'primary_color' => '#0c4589',
        'favicon_url' => '/img/sharp/favicon-32x32.png',
        'logo_urls' => [
            'menu' => '/img/sharp/menu-icon.png',
            'login' => '/img/sharp/login-icon.png',
        ],
    ],

    'login_page_message_blade_path' => env('SHARP_LOGIN_PAGE_MESSAGE_BLADE_PATH', 'sharp/_login-page-message'),

    'extensions' => [
        'assets' => [
            'strategy' => 'raw',
            'head' => [
                '/css/sharp-extension.css',
            ],
        ],
    ],

    'markdown_editor' => [
        'nl2br' => false,
        'tight_lists_only' => true,
    ],
];
