<?php

use Code16\Sharp\Tests\Fixtures\Person;

beforeEach(function () {
    login();
});

it('can generate a new entity from console', function () {
    $person = Person::create(['name' => 'Marie Curie']);

    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A complete entity (with list, form, etc)')
        ->expectsQuestion('What is the type of your entity?', 'Classic')
        ->expectsQuestion('What is the name of your entity?', 'Toto')
        ->expectsQuestion('What is the path of your models directory?', 'Models')
        ->expectsQuestion('What is the label of your entity?', 'Titis')
        ->expectsQuestion('What do you need with your entity?', 'List, form & show page')
        ->expectsConfirmation('Do you need a policy?', 'yes')
        ->assertExitCode(0);

    $this->get(route('code16.sharp.list', ['totos']))
        ->assertOk();

    $this->get(route('code16.sharp.show.show', [
        'uri' => 's-list/totos',
        'entityKey' => 'totos',
        'instanceId' => $person->id,
    ]))
        ->assertOk();

    $this->get(route('code16.sharp.form.edit', [
        'uri' => 's-list/totos',
        'entityKey' => 'totos',
        'instanceId' => $person->id,
    ]))
        ->assertOk();
});
