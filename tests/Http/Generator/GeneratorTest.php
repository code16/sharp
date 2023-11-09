<?php

use Code16\Sharp\Tests\Fixtures\ClosedPeriod;

beforeEach(function () {
    login();
});

it('can generate a new full sharp entity from console and we can create, display, update and delete an item', function () {
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A complete entity (with list, form, etc)')
        ->expectsQuestion('What is the type of your entity?', 'Classic')
        ->expectsQuestion('What is the name of your entity?', 'ClosedPeriod')
        ->expectsQuestion('What is the path of your models directory?', 'Models')
        ->expectsQuestion('What is the label of your entity?', 'Fermetures')
        ->expectsQuestion('What do you need with your entity?', 'List, form & show page')
        ->expectsConfirmation('Do you need a policy?', 'yes')
        ->assertExitCode(0);

//    dd($this->get(route('code16.sharp.list', ['closed_periods'])));
//        ->assertOk();
    // @todo find why we can't display an empty list

    $this->get(route('code16.sharp.form.create', [
        'uri' => 's-list/closed_periods',
        'entityKey' => 'closed_periods',
    ]))
        ->assertOk();

    $this->post(route('code16.sharp.form.store', [
        'uri' => 's-list/closed_periods',
        'entityKey' => 'closed_periods',
    ]), [
        'my_field' => 'Arnaud',
    ])
        ->assertStatus(302);

    $this->assertDatabaseHas('closed_periods', ['my_field' => 'Arnaud']);

    $this->get(route('code16.sharp.list', ['closed_periods']))
        ->assertOk()
        ->assertSee('Arnaud');

    $closedPeriod = ClosedPeriod::first();

    $this->get(route('code16.sharp.show.show', [
        'uri' => 's-list/closed_periods',
        'entityKey' => 'closed_periods',
        'instanceId' => $closedPeriod->id,
    ]))
        ->assertOk()
        ->assertSee('Arnaud');

    $this->get(route('code16.sharp.form.edit', [
        'uri' => 's-list/closed_periods',
        'entityKey' => 'closed_periods',
        'instanceId' => $closedPeriod->id,
    ]))
        ->assertOk();

    $this->post(route('code16.sharp.form.update', [
        'uri' => 's-list/closed_periods',
        'entityKey' => 'closed_periods',
        'instanceId' => $closedPeriod->id,
    ]), [
        'my_field' => 'Benoit',
    ])
        ->assertStatus(302);

    $this->assertDatabaseHas('closed_periods', ['my_field' => 'Benoit']);

    $this->delete(route('code16.sharp.show.delete', [
        'uri' => 's-list/closed_periods',
        'entityKey' => 'closed_periods',
        'instanceId' => $closedPeriod->id,
    ]))
        ->assertStatus(302);

    $this->assertDatabaseMissing('closed_periods', ['id' => $closedPeriod->id]);
});

it('can generate a new sharp single entity from console', function () {

    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A complete entity (with list, form, etc)')
        ->expectsQuestion('What is the type of your entity?', 'Single')
        ->expectsQuestion('What is the name of your entity?', 'Settings')
        ->expectsQuestion('What is the label of your entity?', 'Configuration')
        ->expectsConfirmation('Do you need a policy?', 'yes')
        ->assertExitCode(0);

    $this->get(route('code16.sharp.single-show', [
        'entityKey' => 'settings',
    ]))
        ->assertOk();

    $this->get(route('code16.sharp.form.create', [
        'uri' => 's-show/settings',
        'entityKey' => 'settings',
    ]))
        ->assertOk();

    $this->post(route('code16.sharp.form.store', [
        'uri' => 's-show/settings',
        'entityKey' => 'settings',
    ]), [])
        ->assertStatus(302);
});

it('can generate a new sharp dashboard from console', function () {

    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A complete entity (with list, form, etc)')
        ->expectsQuestion('What is the type of your entity?', 'Dashboard')
        ->expectsQuestion('What is the name of your dashboard?', 'Financial')
        ->expectsConfirmation('Do you need a policy?', 'yes')
        ->assertExitCode(0);

    $this->get(route('code16.sharp.dashboard', [
        'dashboardKey' => 'financial',
    ]))
        ->assertOk()
        ->assertSee('My section title')
        ->assertSee('1234');
});
