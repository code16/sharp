<?php

namespace Code16\Sharp\Tests\Feature\Api\Embeds;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

class EmbedsFormControllerTest extends BaseApiTest
{
    /** @test */
    public function we_can_get_fields_and_layout_of_an_embed()
    {
        $this->buildTheWorld();

        $name = Str::random();

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.form.show', [EmbedsFormControllerTestEmbed::$key, 'person', 1]),
                [
                    'name' => $name
                ]
            )
            ->assertOk()
            ->assertJson([
                'fields' => [
                    'name' => [
                        'key' => 'name',
                        'type' => 'text',
                        'inputType' => 'text',
                    ]
                ],
                'layout' => [
                    [
                        [
                            'key' => 'name',
                            'size' => 12,
                            'sizeXS' => 12,
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function we_can_get_transformed_and_formatted_data_of_an_embed()
    {
        $this->buildTheWorld();

        $name = Str::random();
        $bio = Str::random();

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.form.show', [EmbedsFormControllerTestEmbed::$key, 'person', 1]),
                [
                    'name' => $name,
                    'bio' => $bio,
                    'another' => 'thing',
                ]
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'name' => $name,
                    'bio' => [
                        'text' => $bio
                    ]
                ]
            ]);
    }

    /** @test */
    public function we_can_not_show_an_embed_without_entity_permission()
    {
        $this->buildTheWorld();

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setPolicy(EmbedsFormControllerTestEntityDenyPolicy::class);

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.form.show', [EmbedsFormControllerTestEmbed::$key, 'person', 1]),
            )
            ->assertStatus(403);

        $this
            ->postJson(
                route('code16.sharp.api.embed.form.show', [EmbedsFormControllerTestEmbed::$key, 'person']),
            )
            ->assertStatus(403);
    }

    /** @test */
    public function we_can_not_show_an_embed_without_view_permission()
    {
        $this->buildTheWorld();

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setPolicy(EmbedsFormControllerTestViewDenyOddPolicy::class);

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.form.show', [EmbedsFormControllerTestEmbed::$key, 'person', 1]),
            )
            ->assertStatus(403);

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.form.show', [EmbedsFormControllerTestEmbed::$key, 'person', 2]),
            )
            ->assertOk();

        $this
            ->postJson(
                route('code16.sharp.api.embed.form.show', [EmbedsFormControllerTestEmbed::$key, 'person']),
            )
            ->assertOk();
    }
}

class EmbedsFormControllerTestEmbed extends SharpFormEditorEmbed
{
    public static string $key = 'Code16.Sharp.Tests.Feature.Api.Embeds.EmbedsFormControllerTestEmbed';

    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-test');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(SharpFormTextField::make('name'))
            ->addField(SharpFormEditorField::make('bio'));
    }

    public function updateContent(array $data = []): array
    {
    }
}

class EmbedsFormControllerTestEntityDenyPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return false;
    }
}

class EmbedsFormControllerTestViewDenyOddPolicy extends SharpEntityPolicy
{
    public function view($user, $instanceId): bool
    {
        return $instanceId%2 === 0;
    }
}