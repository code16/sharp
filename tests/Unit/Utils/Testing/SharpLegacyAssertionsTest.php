<?php

use Code16\Sharp\Utils\Testing\SharpAssertions;

uses(SharpAssertions::class);

it('allows to test getSharpForm for edit with a custom breadcrumb', function () {
    $response = fakeResponse()
        ->withSharpCurrentBreadcrumb(
            ['list', 'leaves'],
            ['show', 'leaves', 6],
        )
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        route('code16.sharp.form.edit', ['s-list/leaves/s-show/leaves/6', 'leaves', 6]),
        $response->uri,
    );
});

it('allows to define a current breadcrumb', function () {
    $response = fakeResponse()
        ->withSharpCurrentBreadcrumb(
            ['list', 'trees'],
            ['show', 'trees', 2],
            ['show', 'leaves', 6],
        )
        ->getSharpForm('leaves', 6);

    $this->assertEquals(
        'http://localhost/sharp/s-list/trees/s-show/trees/2/s-show/leaves/6/s-form/leaves/6',
        $response->uri,
    );
});
