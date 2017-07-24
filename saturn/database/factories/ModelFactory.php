<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'group' => 'user',
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\SpaceshipType::class, function (Faker\Generator $faker) {
    return [
        'label' => $faker->word,
    ];
});

$factory->define(\App\Spaceship::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->lastName,
        'capacity' => $faker->numberBetween(5, 80) * 1000,
        'construction_date' => $faker->date(),
        'type_id' => factory(\App\SpaceshipType::class)->create()->id
    ];
});

$factory->define(\App\Pilot::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
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