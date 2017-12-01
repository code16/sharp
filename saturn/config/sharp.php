<?php

return [

    "name" => "Saturn",

    "entities" => [
        "spaceship" => [
            "list" => \App\Sharp\SpaceshipSharpList::class,
            "form" => \App\Sharp\SpaceshipSharpForm::class,
            "validator" => \App\Sharp\SpaceshipSharpValidator::class,
            "policy" => \App\Sharp\Policies\SpaceshipPolicy::class,
        ],
        "pilot" => [
            "list" => \App\Sharp\PilotSharpList::class,
            "form" => \App\Sharp\PilotSharpForm::class,
            "validator" => \App\Sharp\PilotSharpValidator::class,
        ],
        "passenger" => [
            "list" => \App\Sharp\PassengerSharpList::class,
            "form" => \App\Sharp\PassengerSharpForm::class,
            "validator" => \App\Sharp\PassengerSharpValidator::class,
        ],
        "travel" => [
            "list" => \App\Sharp\TravelSharpList::class,
            "form" => \App\Sharp\TravelSharpForm::class,
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
    ],

    "menu" => [
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