<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'group' => 'user',
        'remember_token' => Str::random(10),
    ];
});

$factory->define(\App\SpaceshipType::class, function (Faker\Generator $faker) {
    $brands = [];
    for($k=0; $k<5; $k++) {
        $brand = $faker->unique()->word;

        for($i=0; $i<rand(4,10); $i++) {
            $brands[$brand][] = $faker->words(2, true);
        }
    }

    return [
        'label' => $faker->word,
        'brands' => $brands
    ];
});

$factory->define(\App\Feature::class, function (Faker\Generator $faker) {
    $type = $faker->randomElement(array_keys(\App\Feature::TYPES));
    $subtype = $faker->randomElement(array_keys(\App\Feature::SUBTYPES[$type]));

    return [
        'name' => $faker->word,
        'type' =>  $type,
        'subtype' =>  $subtype,
    ];
});

$factory->define(\App\Corporation::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
    ];
});

$factory->define(\App\Spaceship::class, function (Faker\Generator $faker) {
    return [
        'name' => [
            "fr" => $faker->lastName,
            "en" => $faker->lastName,
        ],
        'description' => $faker->realText(400),
        'capacity' => $faker->numberBetween(5, 80) * 1000,
        'construction_date' => $faker->date(),
        'state' => $faker->randomElement(["inactive", "active", "building", "conception"]),
        'type_id' => function() {
            return factory(\App\SpaceshipType::class)->create()->id;
        },
        'corporation_id' => function() {
            return factory(\App\Corporation::class)->create()->id;
        }
    ];
});

$factory->define(\App\Travel::class, function (Faker\Generator $faker) {
    return [
        'departure_date' => $faker->dateTimeInInterval('-10 years', '+10 years'),
        'spaceship_id' => function() {
            return (\App\Spaceship::class)->create()->id;
        },
        'description' => $faker->text,
        'destination' => $faker->country,
        'destination_coordinates' => $faker->latitude . "," . $faker->longitude
    ];
});

$factory->define(\App\Passenger::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'gender' => $faker->boolean ? 'M' : 'F',
        'birth_date' => $faker->date(),
        'travel_id' => function() {
            return (\App\Travel::class)->create()->id;
        },
        'travel_category' => $faker->randomElement(['Third class', 'Classic', 'First class', 'Business']),
    ];
});

$factory->define(\App\Pilot::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'role' => $faker->randomElement(["jr", "sr"]),
        'xp' => $faker->numberBetween(1, 10)
    ];
});

$factory->define(\App\TechnicalReview::class, function (Faker\Generator $faker) {
    $status = $faker->boolean ? "ok" : "ko";

    return [
        'status' => $status,
        'comment' => $status == "ko" ? $faker->sentence : null,
        'starts_at' => $faker->dateTimeThisYear
    ];
});