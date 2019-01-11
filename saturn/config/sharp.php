<?php

return [

    "name" => "Saturn",

    "custom_url_segment" => "admin",

    "extensions" => [
        "assets" => [
            "strategy" => "asset",
            "head" => [
                "/css/inject.css",
            ],
        ],

        "activate_custom_form_fields" => env("SHARP_CUSTOM_FORM_FIELDS", true),
    ],

    "entities" => [
        "spaceship" => [
            "list" => \App\Sharp\SpaceshipSharpList::class,
            "form" => \App\Sharp\SpaceshipSharpForm::class,
            "validator" => \App\Sharp\SpaceshipSharpValidator::class,
            "policy" => \App\Sharp\Policies\SpaceshipPolicy::class,
        ],
        "pilot" => [
            "list" => \App\Sharp\PilotSharpList::class,
            "forms" => [
                "junior" => [
                    "icon" => "fa-user-o",
                    "label" => "Junior Pilot",
                    "form" => \App\Sharp\PilotJuniorSharpForm::class,
                    "validator" => \App\Sharp\PilotJuniorSharpValidator::class,
                ],
                "senior" => [
                    "icon" => "fa-user",
                    "label" => "Senior Pilot",
                    "form" => \App\Sharp\PilotSeniorSharpForm::class,
                    "validator" => \App\Sharp\PilotSeniorSharpValidator::class,
                ]
            ],
        ],
        "passenger" => [
            "list" => \App\Sharp\PassengerSharpList::class,
            "form" => \App\Sharp\PassengerSharpForm::class,
            "validator" => \App\Sharp\PassengerSharpValidator::class,
        ],
        "travel" => [
            "list" => \App\Sharp\TravelSharpList::class,
            "form" => \App\Sharp\TravelSharpForm::class,
            "validator" => \App\Sharp\TravelSharpValidator::class,
        ],
        "user" => [
            "list" => \App\Sharp\UserSharpList::class,
            "policy" => \App\Sharp\Policies\UserPolicy::class,
            "authorizations" => [
                "delete" => false,
                "create" => false,
                "update" => false,
                "view" => false
            ]
        ],
        "feature" => [
            "list" => \App\Sharp\FeatureSharpList::class,
            "form" => \App\Sharp\FeatureSharpForm::class,
            "validator" => \App\Sharp\FeatureSharpValidator::class,
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

    "menu" => [
        [
            "label" => "Company",
            "entities" => [
                [
                    "label" => "Dashboard",
                    "icon" => "fa-dashboard",
                    "dashboard" => "company_dashboard"
                ],
                [
                    "label" => "Spaceships",
                    "icon" => "fa-space-shuttle",
                    "entity" => "spaceship"
                ],
                [
                    "label" => "Pilots",
                    "icon" => "fa-user",
                    "entity" => "pilot"
                ]
            ]
        ], [
            "label" => "Travels",
            "entities" => [
                [
                    "label" => "Dashboard",
                    "icon" => "fa-dashboard",
                    "dashboard" => "travels_dashboard"
                ],
                [
                    "label" => "Passengers",
                    "icon" => "fa-bed",
                    "entity" => "passenger"
                ],
                [
                    "label" => "Travel",
                    "icon" => "fa-suitcase",
                    "entity" => "travel"
                ],
                [
                    "label" => "Some external link",
                    "icon" => "fa-globe",
                    "url" => "https://google.com"
                ],
            ]
        ], [
            "label" => "Admin",
            "entities" => [
                [
                    "label" => "Sharp users",
                    "icon" => "fa-user-secret",
                    "entity" => "user"
                ]
            ]
        ],
        [
            "label" => "Public website",
            "icon" => "fa-globe",
            "url" => "https://google.com"
        ],
        [
            "label" => "Features",
            "icon" => "fa-superpowers",
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
    ]
];