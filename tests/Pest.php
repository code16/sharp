<?php

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->beforeEach(function () {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedTinyInteger('age')->nullable();
            $table->unsignedTinyInteger('order')->nullable();
            $table->timestamps();
        });
    })
    ->group('eloquent')
    ->in(__DIR__);

function login(?User $user = null)
{
    return test()->actingAs($user ?: new User, config('sharp.auth.guard', 'web'));
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