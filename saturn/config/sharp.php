<?php

return [
    "entities" => [
        "spaceship" => [
            "data" => \App\Sharp\SpaceshipSharpForm::class,
            "form" => \App\Sharp\SpaceshipSharpForm::class,
            "validator" => \App\Sharp\SpaceshipSharpValidator::class,
        ]
    ]
];