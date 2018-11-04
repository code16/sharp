<?php

return [

    "name" => "Saturn",

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

    "menu" => [
        [
            "label" => "External URL",
            "icon" => "fa-globe",
            "url" => "https://google.com"
        ],
        [
            "label" => "Spaceships",
            "icon" => "fa-space-shuttle",
            "entity" => "spaceship"
        ],
        [
            "label" => "Company",
            "entities" => [
                "spaceship" => [
                    "label" => "Spaceships",
                    "icon" => "fa-space-shuttle"
                ],
                "pilot" => [
                    "label" => "Pilots",
                    "icon" => "fa-user"
                ]
            ]
        ], [
            "label" => "Travels",
            "entities" => [
                "passenger" => [
                    "label" => "Passengers",
                    "icon" => "fa-bed"
                ],
                "travel" => [
                    "label" => "Travel",
                    "icon" => "fa-suitcase"
                ]
            ]
        ], [
            "label" => "Admin",
            "entities" => [
                "user" => [
                    "label" => "Sharp users",
                    "icon" => "fa-user-secret"
                ],
                "feature" => [
                    "label" => "Features",
                    "icon" => "fa-superpowers"
                ]
            ]
        ],
    ],

    "dashboard" => \App\Sharp\Dashboard::class,

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