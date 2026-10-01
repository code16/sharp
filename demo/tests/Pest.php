<?php

use Code16\Sharp\Utils\Testing\SharpAssertions;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, LazilyRefreshDatabase::class, SharpAssertions::class)
    ->beforeEach(function () {
        $this->withoutVite();
    })
    ->in('Feature');
