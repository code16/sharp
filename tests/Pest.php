<?php

use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->group('eloquent')
    ->beforeEach(function () {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->unsignedTinyInteger('order')->nullable();
            $table->unsignedInteger('partner_id')->nullable();
            $table->unsignedInteger('chief_id')->nullable();
            $table->timestamps();
        });

        Schema::create('colleagues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person1_id');
            $table->unsignedInteger('person2_id');
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
        });

        Schema::create('pictures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->morphs('picturable');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('taggable_id');
            $table->string('taggable_type');
        });

        Schema::create('sharp_upload_models', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('model');
            $table->string('model_key')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('disk')->default('local');
            $table->unsignedInteger('size')->nullable();
            $table->text('custom_properties')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
        });
    })
    ->in(__DIR__);

function login(?User $user = null)
{
    return test()->actingAs(
        $user ?: new User(),
        sharp()->config()->get('auth.guard') ?: 'web'
    );
}

function fakeListFor(string $entityKey, $fakeImplementation)
{
    app(\Code16\Sharp\Utils\Entities\SharpEntityManager::class)
        ->entityFor($entityKey)
        ->setList($fakeImplementation);

    return test();
}

function fakeShowFor(string $entityKey, $fakeImplementation)
{
    app(\Code16\Sharp\Utils\Entities\SharpEntityManager::class)
        ->entityFor($entityKey)
        ->setShow($fakeImplementation);

    return test();
}

function fakeFormFor(string $entityKey, $fakeImplementation)
{
    app(\Code16\Sharp\Utils\Entities\SharpEntityManager::class)
        ->entityFor($entityKey)
        ->setForm($fakeImplementation);

    return test();
}

function fakePolicyFor(string $entityKey, $fakeImplementation)
{
    app(\Code16\Sharp\Utils\Entities\SharpEntityManager::class)
        ->entityFor($entityKey)
        ->setPolicy($fakeImplementation);

    return test();
}

function createImage(string $disk = 'local', string $name = 'test.png'): string
{
    $file = UploadedFile::fake()->image($name, 600, 600);

    return $file->storeAs('data', $name, ['disk' => $disk]);
}
