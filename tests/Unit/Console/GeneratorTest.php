<?php

use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\Tests\Fixtures\UnitTestModel;
use Illuminate\Database\Schema\Blueprint;

beforeEach(function () {
    login();

    File::deleteDirectory(base_path('app/Sharp'));
});

it('can generate a new full sharp entity from console and we can create, display, update and delete an item', function () {
    Schema::create('unit_test_models', function (Blueprint $table) {
        $table->increments('id');
        $table->string('my_field');
        $table->timestamps();
    });

    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Regular')
        ->expectsQuestion('What is the name of your Entity (singular)?', 'UnitTestModel')
        ->expectsQuestion('Do you want to attach this Entity to a specific Model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsQuestion('What is the label of your Entity?', 'Unit Test Models')
        ->expectsQuestion('What do you need with your Entity?', 'Entity List, Form & Show Page')
        ->expectsConfirmation('Do you need a Policy?', 'yes')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    // Manually add this new Entity to the Sharp config
    app(SharpConfigBuilder::class)
        ->addEntity('unit_test_models', '\App\Sharp\Entities\UnitTestModelEntity');

    $this
        ->get(route('code16.sharp.list', ['entityKey' => 'unit_test_models']))
        ->assertOk();

    $this
        ->get(route('code16.sharp.form.create', [
            'parentUri' => 's-list/unit_test_models',
            'entityKey' => 'unit_test_models',
        ]))
        ->assertOk();

    $this
        ->post(route('code16.sharp.form.store', [
            'parentUri' => 's-list/unit_test_models',
            'entityKey' => 'unit_test_models',
        ]), [
            'my_field' => 'Arnaud',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('unit_test_models', ['my_field' => 'Arnaud']);

    $this->get(route('code16.sharp.list', ['unit_test_models']))
        ->assertOk()
        ->assertSee('Arnaud');

    $unitTestModel = UnitTestModel::first();

    $this
        ->get(route('code16.sharp.show.show', [
            'parentUri' => 's-list/unit_test_models',
            'entityKey' => 'unit_test_models',
            'instanceId' => $unitTestModel->id,
        ]))
        ->assertOk()
        ->assertSee('Arnaud');

    $this
        ->get(route('code16.sharp.form.edit', [
            'parentUri' => 's-list/unit_test_models',
            'entityKey' => 'unit_test_models',
            'instanceId' => $unitTestModel->id,
        ]))
        ->assertOk();

    $this
        ->post(route('code16.sharp.form.update', [
            'parentUri' => 's-list/unit_test_models',
            'entityKey' => 'unit_test_models',
            'instanceId' => $unitTestModel->id,
        ]), [
            'my_field' => 'Benoit',
        ])
        ->assertStatus(302);

    $this->assertDatabaseHas('unit_test_models', ['my_field' => 'Benoit']);

    $this
        ->delete(route('code16.sharp.show.delete', [
            'parentUri' => 's-list/unit_test_models',
            'entityKey' => 'unit_test_models',
            'instanceId' => $unitTestModel->id,
        ]))
        ->assertStatus(302);

    $this->assertDatabaseMissing('unit_test_models', ['id' => $unitTestModel->id]);
});

it('can generate a new sharp single entity from console', function () {
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Single')
        ->expectsQuestion('What is the name of your Entity (singular)?', 'Settings')
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
        ->expectsQuestion('What is the name of your Dashboard (singular)?', 'Financial')
        ->expectsConfirmation('Do you need a Policy?', 'yes')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    // Manually add this new Entity to the Sharp config
    app(SharpConfigBuilder::class)
        ->addEntity('financial', '\App\Sharp\Entities\FinancialEntity');

    $this->get(
        route('code16.sharp.dashboard', [
            'dashboardKey' => 'financial',
        ]))
        ->assertOk()
        ->assertSee('My section title')
        ->assertSee('1234');
});

it('can generate a new command from console', function () {
    Schema::create('unit_test_models', function (Blueprint $table) {
        $table->increments('id');
        $table->string('my_field');
        $table->timestamps();
    });

    // First create an entity
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Regular')
        ->expectsQuestion('What is the name of your Entity (singular)?', 'UnitTestModel')
        ->expectsConfirmation('Do you want to attach this Entity to a specific Model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsQuestion('What is the label of your Entity?', 'Unit Test Models')
        ->expectsQuestion('What do you need with your Entity?', 'Entity List, Form & Show Page')
        ->expectsConfirmation('Do you need a Policy?', 'no')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    app(SharpConfigBuilder::class)
        ->addEntity('unit_test_models', '\App\Sharp\Entities\UnitTestModelEntity');

    // Now generate a command
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A Command')
        ->expectsQuestion('What is the type of the new Command?', 'Instance')
        ->expectsConfirmation('Do you need a Form in the Command?', 'no')
        ->expectsQuestion('What is the name of your Command?', 'SendEmail')
        ->assertExitCode(0);

    expect(File::exists(base_path('app/Sharp/UnitTestModels/Commands/SendEmailCommand.php')))->toBeTrue();
});

it('can generate a new list filter from console', function () {
    Schema::create('unit_test_models', function (Blueprint $table) {
        $table->increments('id');
        $table->string('my_field');
        $table->timestamps();
    });

    // First create an entity
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Regular')
        ->expectsQuestion('What is the name of your Entity (singular)?', 'UnitTestModel')
        ->expectsConfirmation('Do you want to attach this Entity to a specific Model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsQuestion('What is the label of your Entity?', 'Unit Test Models')
        ->expectsQuestion('What do you need with your Entity?', 'Entity List')
        ->expectsConfirmation('Do you need a Policy?', 'no')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    app(SharpConfigBuilder::class)
        ->addEntity('unit_test_models', '\App\Sharp\Entities\UnitTestModelEntity');

    // Now generate a filter
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A List Filter')
        ->expectsQuestion('What is the type of your Entity Filter?', 'Select')
        ->expectsConfirmation('Can the Filter accept multiple values?', 'no')
        ->expectsConfirmation('Can the Filter accept empty value?', 'yes')
        ->expectsQuestion('What is the name of your Filter?', 'Status')
        ->assertExitCode(0);

    expect(File::exists(base_path('app/Sharp/UnitTestModels/Filters/StatusFilter.php')))->toBeTrue();
});

it('can generate a new entity state from console', function () {
    Schema::create('unit_test_models', function (Blueprint $table) {
        $table->increments('id');
        $table->string('my_field');
        $table->timestamps();
    });

    // First create an entity with List and Show
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Regular')
        ->expectsQuestion('What is the name of your Entity (singular)?', 'UnitTestModel')
        ->expectsConfirmation('Do you want to attach this Entity to a specific Model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsQuestion('What is the label of your Entity?', 'Unit Test Models')
        ->expectsQuestion('What do you need with your Entity?', 'Entity List & Show Page')
        ->expectsConfirmation('Do you need a Policy?', 'no')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    app(SharpConfigBuilder::class)
        ->addEntity('unit_test_models', '\App\Sharp\Entities\UnitTestModelEntity');

    // Now generate an entity state
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'An Entity State')
        ->expectsQuestion('What is the name of your Entity State?', 'Approval')
        ->expectsConfirmation('Should the Entity State update an instance of an Eloquent model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsConfirmation('A App\Code16\Sharp\Tests\Fixtures\UnitTestModel model does not exist. Do you want to generate it?', 'no')
        ->assertExitCode(0);

    expect(File::exists(base_path('app/Sharp/UnitTestModels/States/ApprovalEntityState.php')))->toBeTrue();
});

it('can generate a new reorder handler from console', function () {
    Schema::create('unit_test_models', function (Blueprint $table) {
        $table->increments('id');
        $table->string('my_field');
        $table->timestamps();
    });

    // First create an entity
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A new Entity')
        ->expectsQuestion('What is the type of your Entity?', 'Regular')
        ->expectsQuestion('What is the name of your Entity (singular)?', 'UnitTestModel')
        ->expectsConfirmation('Do you want to attach this Entity to a specific Model?', 'yes')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsQuestion('What is the label of your Entity?', 'Unit Test Models')
        ->expectsQuestion('What do you need with your Entity?', 'Entity List')
        ->expectsConfirmation('Do you need a Policy?', 'no')
        ->expectsConfirmation('Do you want to automatically declare this Entity in the Sharp configuration?', 'no')
        ->assertExitCode(0);

    app(SharpConfigBuilder::class)
        ->addEntity('unit_test_models', '\App\Sharp\Entities\UnitTestModelEntity');

    // Now generate a reorder handler
    $this->artisan('sharp:generator')
        ->expectsQuestion('What do you need?', 'A Reorder Handler')
        ->expectsQuestion('What is the namespace of your models?', 'App/Models')
        ->expectsConfirmation('Use the simple Eloquent implementation based on a reorder attribute?', 'no')
        ->expectsQuestion('What is the name of your reorder handler?', 'UnitTestModel')
        ->expectsConfirmation('A App\Code16\Sharp\Tests\Fixtures\UnitTestModel model does not exist. Do you want to generate it?', 'no')
        ->assertExitCode(0);

    expect(File::exists(base_path('app/Sharp/UnitTestModels/ReorderHandlers/UnitTestModelReorder.php')))->toBeTrue();
});
