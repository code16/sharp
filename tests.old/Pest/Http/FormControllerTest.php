<?php

use Inertia\Testing\Assert;

it('can get form data for an entity', function () {
//    config()->set(
//        'sharp.entities.person',
//        PersonEntity::class,
//    );

    $this->get('/sharp/s-list/person/s-show/person/1/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Announcements/Index')
            ->where('name', 'John Wayne')
        );
});