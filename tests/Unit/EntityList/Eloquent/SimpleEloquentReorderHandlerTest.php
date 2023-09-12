<?php

use Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler;
use Code16\Sharp\Tests\Fixtures\Person;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('allows to use SimpleEloquentReorderHandler', function () {
    Person::create(['id' => 10, 'name' => 'Bob', 'order' => 1]);
    Person::create(['id' => 20, 'name' => 'Bob', 'order' => 2]);
    Person::create(['id' => 30, 'name' => 'Bob', 'order' => 3]);

    (new SimpleEloquentReorderHandler(Person::class))
        ->reorder([30, 10, 20]);

    expect(Person::orderBy('order')->pluck('id')->all())
        ->toBe([30, 10, 20]);
})->group('eloquent');

it('we_can_use_SimpleEloquentReorderHandler_with_custom_order_attribute', function () {
    Person::create(['id' => 20, 'name' => 'Bob', 'order' => 3, 'age' => 22]);
    Person::create(['id' => 30, 'name' => 'Bob', 'order' => 2, 'age' => 32]);
    Person::create(['id' => 50, 'name' => 'Bob', 'order' => 1, 'age' => 90]);

    (new SimpleEloquentReorderHandler(Person::class))
        ->setOrderAttribute('age')
        ->reorder([50, 20, 30]);

    expect(Person::orderBy('age')->pluck('id')->all())
        ->toBe([50, 20, 30]);
})->group('eloquent');
