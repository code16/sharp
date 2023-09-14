<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Http\Api\Embeds\Fixtures\ApiEmbedsControllerTestSimpleEmbed;
use Code16\Sharp\Tests\Http\Api\Embeds\Fixtures\ApiEmbedsFormControllerTestEmbed;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('returns fields and layout of an embed', function () {
    $name = Str::random();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 1]),
            [
                'name' => $name,
            ]
        )
        ->assertOk()
        ->assertJson([
            'fields' => [
                'name' => [
                    'key' => 'name',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
            'layout' => [
                [
                    [
                        'key' => 'name',
                        'size' => 12,
                        'sizeXS' => 12,
                    ],
                ],
            ],
        ]);
});

it('returns transformed and formatted data of an embed', function () {
    $name = Str::random();
    $bio = Str::random();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 1]),
            [
                'name' => $name,
                'bio' => $bio,
                'another' => 'thing',
            ]
        )
        ->assertOk()
        ->assertJson([
            'data' => [
                'name' => str($name)->upper()->toString(),
                'bio' => [
                    'text' => $bio,
                ],
            ],
        ]);
});

it('does not show an embed without entity permission', function () {
    fakePolicyFor('person', new class extends SharpEntityPolicy {
        public function entity($user): bool
        {
            return false;
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 1]),
        )
        ->assertStatus(403);

    $this
        ->postJson(
            route('code16.sharp.api.embed.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person']),
        )
        ->assertStatus(403);
});

it('does not show an embed without view permission', function () {
    fakePolicyFor('person', new class extends SharpEntityPolicy {
        public function view($user, $instanceId): bool
        {
            return $instanceId == 2;
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 1]),
        )
        ->assertForbidden();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 2]),
        )
        ->assertOk();

    $this
        ->postJson(
            route('code16.sharp.api.embed.form.show', [ApiEmbedsFormControllerTestEmbed::$key, 'person']),
        )
        ->assertOk();
});

it('updates an embed and get transformed data', function () {
    $name = Str::random();
    $bio = Str::random();

    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.update', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 1]),
            [
                'name' => $name,
                'bio' => ['text' => $bio],
                'another' => 'thing',
            ]
        )
        ->assertOk()
        ->assertJson([
            'name' => $name,
            'bio' => $bio,
        ]);
});

it('validates data when updating an embed', function () {
    $this
        ->postJson(
            route('code16.sharp.api.embed.instance.form.update', [ApiEmbedsFormControllerTestEmbed::$key, 'person', 1]),
            [
                'name' => null,
                'bio' => ['text' => 'aaa'],
            ]
        )
        ->assertJsonValidationErrorFor('name');
});