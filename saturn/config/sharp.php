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
        "spaceship" => [
            "list" => \App\Sharp\SpaceshipSharpList::class,
            "show" => \App\Sharp\SpaceshipSharpShow::class,
            "form" => \App\Sharp\SpaceshipSharpForm::class,
            "validator" => \App\Sharp\SpaceshipSharpValidator::class,
            "policy" => \App\Sharp\Policies\SpaceshipPolicy::class,
            "label" => "Spaceship"
        ],
        "pilot" => [
            "list" => \App\Sharp\PilotSharpList::class,
            "show" => \App\Sharp\PilotSharpShow::class,
            "forms" => [
                "junior" => [
                    "icon" => "far fa-user",
                    "label" => "Junior Pilot",
                    "form" => \App\Sharp\PilotJuniorSharpForm::class,
                    "validator" => \App\Sharp\PilotJuniorSharpValidator::class,
                ],
                "senior" => [
                    "icon" => "fas fa-user",
                    "label" => "Senior Pilot",
                    "form" => \App\Sharp\PilotSeniorSharpForm::class,
                    "validator" => \App\Sharp\PilotSeniorSharpValidator::class,
                ]
            ],
            "label" => "Pilot"
        ],
        "spaceship_pilot" => [
            "list" => \App\Sharp\EmbeddedEntityLists\SpaceshipPilotSharpList::class,
            "show" => \App\Sharp\PilotSharpShow::class,
            "forms" => [
                "junior" => [
                    "icon" => "far fa-user",
                    "label" => "Junior Pilot",
                    "form" => \App\Sharp\PilotJuniorSharpForm::class,
                    "validator" => \App\Sharp\PilotJuniorSharpValidator::class,
                ],
                "senior" => [
                    "icon" => "fas fa-user",
                    "label" => "Senior Pilot",
                    "form" => \App\Sharp\PilotSeniorSharpForm::class,
                    "validator" => \App\Sharp\PilotSeniorSharpValidator::class,
                ]
            ],
            "label" => "Pilot"
        ],
        "passenger" => [
            "list" => \App\Sharp\PassengerSharpList::class,
            "form" => \App\Sharp\PassengerSharpForm::class,
            "validator" => \App\Sharp\PassengerSharpValidator::class,
            "label" => "Passenger"
        ],
        "travel" => [
            "list" => \App\Sharp\TravelSharpList::class,
            "form" => \App\Sharp\TravelSharpForm::class,
            "validator" => \App\Sharp\TravelSharpValidator::class,
            "label" => "Travel"
        ],
        "user" => [
            "list" => \App\Sharp\UserSharpList::class,
            "policy" => \App\Sharp\Policies\UserPolicy::class,
            "authorizations" => [
                "delete" => false,
                "create" => false,
                "update" => false,
                "view" => false
            ],
            "label" => "Sharp user"
        ],
        "account" => [
            "show" => \App\Sharp\AccountSharpShow::class,
            "form" => \App\Sharp\AccountSharpForm::class,
            "label" => "My account"
        ],
        "feature" => [
            "list" => \App\Sharp\FeatureSharpList::class,
            "form" => \App\Sharp\FeatureSharpForm::class,
            "validator" => \App\Sharp\FeatureSharpValidator::class,
            "label" => "Feature"
        ],
        "test" => [
            "form" => \App\Sharp\TestForm\TestForm::class,
        ],
    ],

    "dashboards" => [
        "company_dashboard" => [
            "view" => \App\Sharp\CompanyDashboard::class,
            "policy" => \App\Sharp\Policies\CompanyDashboardPolicy::class,
        ],
        "travels_dashboard" => [
            "view" => \App\Sharp\TravelsDashboard::class,
        ],
    ],

    "global_filters" => [
        "corporation" => \App\Sharp\Filters\CorporationGlobalFilter::class
    ],

    "menu" => [
        [
            "label" => "Company",
            "entities" => [
                [
                    "label" => "Dashboard",
                    "icon" => "fas fa-tachometer-alt",
                    "dashboard" => "company_dashboard"
                ],
                [
                    "label" => "Spaceships",
                    "icon" => "fas fa-space-shuttle",
                    "entity" => "spaceship"
                ],
                [
                    "label" => "Pilots",
                    "icon" => "fas fa-user",
                    "entity" => "pilot"
                ]
            ]
        ], [
            "label" => "Travels",
            "entities" => [
                [
                    "label" => "Dashboard",
                    "icon" => "fas fa-tachometer-alt",
                    "dashboard" => "travels_dashboard"
                ],
                [
                    "label" => "Passengers",
                    "icon" => "fas fa-bed",
                    "entity" => "passenger"
                ],
                [
                    "label" => "Travel",
                    "icon" => "fas fa-suitcase",
                    "entity" => "travel"
                ],
                [
                    "label" => "Some external link",
                    "icon" => "fas fa-globe",
                    "url" => "https://google.com"
                ],
            ]
        ], [
            "label" => "Admin",
            "entities" => [
                [
                    "label" => "My account",
                    "icon" => "fas fa-user",
                    "entity" => "account",
                    "single" => true
                ],
                [
                    "label" => "Sharp users",
                    "icon" => "fas fa-user-secret",
                    "entity" => "user"
                ]
            ]
        ],
        [
            "label" => "Public website",
            "icon" => "fas fa-globe",
            "url" => "https://google.com"
        ],
        [
            "label" => "Some powerful Features",
            "icon" => "fab fa-superpowers",
            "entity" => "feature"
        ]
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
//        "primary_color" => "#004c9b",
        "primary_color" => "#400f9b"
    ],
];
