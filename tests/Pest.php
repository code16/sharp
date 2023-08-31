<?php

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function login()
{
    return test()->actingAs(new User);
}

function fakeFormFor(string $entityKey, $fakeImplementation)
{
    app(\Code16\Sharp\Utils\Entities\SharpEntityManager::class)
        ->entityFor($entityKey)
        ->setForm($fakeImplementation);

    return test();
}