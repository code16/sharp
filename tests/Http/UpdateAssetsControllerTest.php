<?php

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use Illuminate\Support\Facades\Event;

uses(WithConsoleEvents::class);

beforeEach(function () {
    login();
});

it('allows to update assets', function () {
    $commandFinishedEvent = null;
    Event::listen(function (CommandFinished $event) use (&$commandFinishedEvent) {
        $commandFinishedEvent = $event;
    });

    $this
        ->from('/sharp/s-list/person')
        ->post(route('code16.sharp.update-assets'))
        ->assertRedirect('/sharp/s-list/person')
        ->assertSessionHas('sharp_notifications');

    expect($commandFinishedEvent)
        ->toBeInstanceOf(CommandFinished::class)
        ->and($commandFinishedEvent->command)->toBe('vendor:publish')
        ->and($commandFinishedEvent->exitCode)->toBe(0);

    $notifications = session('sharp_notifications');
    $notification = collect($notifications)->first();
    expect($notification['title'])->toBe('Assets updated successfully')
        ->and($notification['level']->value)->toBe('success');
});
