<?php

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function login()
{
    return test()->actingAs(new User);
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