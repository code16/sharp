<?php

use Code16\Sharp\Tests\Fixtures\ClosedPeriod;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    login();
});

it('can generate a new full sharp entity from console and we can create, display, update and delete an item', function () {
    if(file_exists(base_path('app/Sharp/Entities/ClosedPeriodEntity.php'))) {
        unlink(base_path('app/Sharp/Entities/ClosedPeriodEntity.php'));
    }

    Schema::create('closed_periods', function (Blueprint $table) {
        $table->increments('id');
        $table->string('my_field');
        $table->timestamps();
    });

    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Regular')
        ->expectsQuestion('What is the name of your Entity?', 'ClosedPeriod')
        ->expectsQuestion('Do you want to attach this Entity to a specific Model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsQuestion('What is the label of your Entity?', 'Fermetures')
        ->expectsQuestion('What do you need with your Entity?', 'Entity List, Form & Show Page')
        ->expectsConfirmation('Do you need a Policy?', 'yes')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    // Manually add this new Entity to the Sharp config
    app(\Code16\Sharp\Config\SharpConfigBuilder::class)
        ->addEntity('closed_periods', '\App\Sharp\Entities\ClosedPeriodEntity');

    $this
        ->get(route('code16.sharp.list', ['entityKey' => 'closed_periods']))
        ->assertOk();

    $this
        ->get(route('code16.sharp.form.create', [
            'parentUri' => 's-list/closed_periods',
            'entityKey' => 'closed_periods',
        ]))
        ->assertOk();

    $this
        ->post(route('code16.sharp.form.store', [
            'parentUri' => 's-list/closed_periods',
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

    $this
        ->get(route('code16.sharp.show.show', [
            'parentUri' => 's-list/closed_periods',
            'entityKey' => 'closed_periods',
            'instanceId' => $closedPeriod->id,
        ]))
        ->assertOk()
        ->assertSee('Arnaud');

    $this
        ->get(route('code16.sharp.form.edit', [
            'parentUri' => 's-list/closed_periods',
            'entityKey' => 'closed_periods',
            'instanceId' => $closedPeriod->id,
        ]))
        ->assertOk();

    $this
        ->post(route('code16.sharp.form.update', [
            'parentUri' => 's-list/closed_periods',
            'entityKey' => 'closed_periods',
            'instanceId' => $closedPeriod->id,
        ]), [
            'my_field' => 'Benoit',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('closed_periods', ['my_field' => 'Benoit']);

    $this
        ->delete(route('code16.sharp.show.delete', [
            'parentUri' => 's-list/closed_periods',
            'entityKey' => 'closed_periods',
            'instanceId' => $closedPeriod->id,
        ]))
        ->assertStatus(302);

    $this->assertDatabaseMissing('closed_periods', ['id' => $closedPeriod->id]);
});

it('can generate a new sharp single entity from console', function () {
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Single')
        ->expectsQuestion('What is the name of your Entity?', 'Settings')
        ->expectsQuestion('What is the label of your Entity?', 'Configuration')
        ->expectsConfirmation('Do you need a Policy?', 'yes')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    // Manually add this new Entity to the Sharp config
    app(\Code16\Sharp\Config\SharpConfigBuilder::class)
        ->addEntity('settings', '\App\Sharp\Entities\SettingsEntity');

    $this->get(
        route('code16.sharp.single-show', [
            'entityKey' => 'settings',
        ]))
        ->assertOk();

    $this
        ->get(route('code16.sharp.form.create', [
            'parentUri' => 's-show/settings',
            'entityKey' => 'settings',
        ]))
        ->assertOk();

    $this->post(
        route('code16.sharp.form.store', [
            'parentUri' => 's-show/settings',
            'entityKey' => 'settings',
        ]), [
            'my_field' => 'Arnaud',
        ])
        ->assertStatus(302);
});

it('can generate a new sharp dashboard from console', function () {
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Dashboard')
        ->expectsQuestion('What is the name of your Dashboard?', 'Financial')
        ->expectsConfirmation('Do you need a Policy?', 'yes')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    // Manually add this new Entity to the Sharp config
    app(\Code16\Sharp\Config\SharpConfigBuilder::class)
        ->addEntity('financial', '\App\Sharp\Entities\FinancialDashboardEntity');

    $this->get(
        route('code16.sharp.dashboard', [
            'dashboardKey' => 'financial',
        ]))
        ->assertOk()
        ->assertSee('My section title')
        ->assertSee('1234');
});
