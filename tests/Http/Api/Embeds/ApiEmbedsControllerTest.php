<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Http\Api\Embeds\Fixtures\ApiEmbedsControllerTestSimpleEmbed;
use Code16\Sharp\Tests\Http\Api\Embeds\Fixtures\ApiEmbedsControllerTestFormattedEmbed;
use Illuminate\Support\Str;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('returns identical data for an embed by default', function () {
    $text = Str::random();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.show', [ApiEmbedsControllerTestSimpleEmbed::$key, 'person', 1]),
            [
                'embeds' => [
                    ['text' => $text],
                ],
            ]
        )
        ->assertOk()
        ->assertJson([
            'embeds' => [
                ['text' => $text],
            ],
        ]);
});

it('allows to format the data of the embed', function () {
    $text = Str::random();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.show', [ApiEmbedsControllerTestFormattedEmbed::$key, 'person', 1]),
            [
                'embeds' => [
                    ['text' => $text],
                ],
            ]
        )
        ->assertOk()
        ->assertJson([
            'embeds' => [
                [
                    'text' => $text,
                    'formatted' => str($text)->upper()->toString(),
                ],
            ],
        ]);
});

it('returns data for multiple embeds', function () {
    $texts = collect([Str::random(), Str::random(), Str::random()]);

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.show', [ApiEmbedsControllerTestSimpleEmbed::$key, 'person', 1]),
            [
                'embeds' => $texts
                    ->map(fn ($text) => ['text' => $text])
                    ->toArray(),
            ]
        )
        ->assertOk()
        ->assertJson([
            'embeds' => $texts
                ->map(fn ($text) => [
                    'text' => $text,
                ])
                ->toArray(),
        ]);
});

it('distinguishes templates with the form param', function () {
    $text = Str::random();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.show', [ApiEmbedsControllerTestFormattedEmbed::$key, 'person', 1]),
            [
                'form' => true,
                'embeds' => [
                    ['text' => $text],
                ],
            ]
        )
        ->assertOk()
        ->assertJsonFragment([
            'form' => true,
        ]);
});
