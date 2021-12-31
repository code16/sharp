<?php

return [

    "name" => "Saturn",

//    "custom_url_segment" => "admin",
    "display_sharp_version_in_title" => true,
    "display_breadcrumb" => true,

    "extensions" => [
        "assets" => [
            "strategy" => "asset",
            "head" => [
                "/css/inject.css",
            ],
        ],

        "activate_custom_fields" => env("SHARP_CUSTOM_FIELDS", true),
    ],

    "entities" => [
        "spaceship" => \App\Sharp\Entities\SpaceshipEntity::class,
        "pilot" => \App\Sharp\Entities\PilotEntity::class, 
        "spaceship_pilot" => \App\Sharp\Entities\SpaceshipPilotEntity::class,
        "passenger" => \App\Sharp\Entities\PassengerEntity::class,
        "travel" => \App\Sharp\Entities\TravelEntity::class,
        "user" => App\Sharp\Entities\UserEntity::class,
        "account" => App\Sharp\Entities\AccountEntity::class,
        
        // Keep the legacy config way for this one: 
        "feature" => [
            "list" => \App\Sharp\FeatureSharpList::class,
            "form" => \App\Sharp\FeatureSharpForm::class,
            "validator" => \App\Sharp\FeatureSharpValidator::class,
            "label" => "Feature"
        ],
        
        "test" => \App\Sharp\Entities\TestEntity::class,
    ],

    "dashboards" => [
        "company_dashboard" => \App\Sharp\Entities\CompanyDashboardEntity::class,
        "travels_dashboard" => \App\Sharp\Entities\TravelsDashboardEntity::class,
    ],

    "global_filters" => [
        \App\Sharp\Filters\CorporationGlobalFilter::class
    ],

    "menu" => \App\Sharp\SharpMenu::class,
//    [
//        [
//            "label" => "Company",
//            "entities" => [
//                [
//                    "label" => "Dashboard",
//                    "icon" => "fas fa-tachometer-alt",
//                    "dashboard" => "company_dashboard"
//                ],
//                [
//                    "label" => "Spaceships",
//                    "icon" => "fas fa-space-shuttle",
//                    "entity" => "spaceship"
//                ],
//                [
//                    "label" => "Pilots",
//                    "icon" => "fas fa-user",
//                    "entity" => "pilot"
//                ]
//            ]
//        ], [
//            "label" => "Travels",
//            "entities" => [
//                [
//                    "label" => "Dashboard",
//                    "icon" => "fas fa-tachometer-alt",
//                    "dashboard" => "travels_dashboard"
//                ],
//                [
//                    "label" => "Passengers",
//                    "icon" => "fas fa-bed",
//                    "entity" => "passenger"
//                ],
//                [
//                    "label" => "Travel",
//                    "icon" => "fas fa-suitcase",
//                    "entity" => "travel"
//                ],
////                [
////                    "separator" => true,
////                    "label" => "Utilities",
////                ],
////                [
////                    "label" => "Some external link",
////                    "icon" => "fas fa-globe",
////                    "url" => "https://google.com",
////                ],
//            ]
//        ], [
//            "label" => "Admin",
//            "entities" => [
//                [
//                    "label" => "My account",
//                    "icon" => "fas fa-user",
//                    "entity" => "account",
//                    "single" => true
//                ],
//                [
//                    "label" => "Sharp users",
//                    "icon" => "fas fa-user-secret",
//                    "entity" => "user"
//                ]
//            ]
//        ],
////        [
////            "label" => "Public website",
////            "icon" => "fas fa-globe",
////            "url" => "https://google.com",
////        ],
//        [
//            "label" => "Some powerful Features",
//            "icon" => "fab fa-superpowers",
//            "entity" => "feature"
//        ]
//    ],
    
    "markdown_editor" => [
        // If false, the UL tool will display a dropdown to choose between tight and normal lists
        "tight_lists_only" => false,
        // If false, simple carriage return will not be converted to <br> (in Sharp)
        "nl2br" => false,
    ],

    "uploads" => [
        "tmp_dir" => env("SHARP_UPLOADS_TMP_DIR", "tmp"),
        "thumbnails_dir" => env("SHARP_UPLOADS_THUMBS_DIR", "thumbnails"),
    ],

    "auth" => [
//        "guard" => "sharp",
        "check_handler" => \App\Sharp\Auth\SharpCheckHandler::class,
        "login_attribute" => "email",
        "password_attribute" => "password",
        "display_attribute" => "name",
    ],

    "login_page_message_blade_path" => env("SHARP_LOGIN_PAGE_MESSAGE_BLADE_PATH", "sharp/_login-page-message"),
    
    "theme" => [
        "primary_color" => env('SHARP_PRIMARY_COLOR', "#004c9b"),
        "favicon_url" => "/sharp-img/favicon.png",
        "logo_urls" => [
            "menu" => "/sharp-img/menu-saturn-icon.png",
//            "login" => "/sharp-img/menu-saturn-icon.png",
        ]
    ],
];
