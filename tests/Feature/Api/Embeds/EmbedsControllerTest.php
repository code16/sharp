<?php

namespace Code16\Sharp\Tests\Feature\Api\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Str;

class EmbedsControllerTest extends BaseApiTest
{
    /** @test */
    public function we_can_get_data_of_an_embed_which_is_identical_by_default()
    {
        $this->buildTheWorld();

        EmbedsControllerTestEmbed::$templateTransformMode = 'none';
        $text = Str::random();

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.show', [EmbedsControllerTestEmbed::$key, 'person', 1]),
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
    }

    /** @test */
    public function we_can_get_updated_data_of_an_embed()
    {
        $this->buildTheWorld();

        EmbedsControllerTestEmbed::$templateTransformMode = 'upper';
        $text = Str::random();

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.show', [EmbedsControllerTestEmbed::$key, 'person', 1]),
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
                        'formatted' => Str::upper($text),
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_can_get_data_for_multiple_embeds()
    {
        $this->buildTheWorld();

        EmbedsControllerTestEmbed::$templateTransformMode = 'lower';
        $texts = collect([Str::random(), Str::random(), Str::random()]);

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.show', [EmbedsControllerTestEmbed::$key, 'person', 1]),
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
                        'formatted' => Str::lower($text),
                    ])
                    ->toArray(),
            ]);
    }

    /** @test */
    public function the_form_param_allows_to_distinguish_templates()
    {
        $this->buildTheWorld();

        EmbedsControllerTestEmbed::$templateTransformMode = 'upper';
        $text = Str::random();

        $this
            ->postJson(
                route('code16.sharp.api.embed.instance.show', [EmbedsControllerTestEmbed::$key, 'person', 1]),
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
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setForm(PersonWithEmbedsForm::class);
    }
}

class PersonWithEmbedsForm extends PersonSharpForm
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormEditorField::make('editor')
                ->allowEmbeds([
                    EmbedsControllerTestEmbed::class,
                ])
        );
    }
}

class EmbedsControllerTestEmbed extends SharpFormEditorEmbed
{
    public static string $key = 'Code16.Sharp.Tests.Feature.Api.Embeds.EmbedsControllerTestEmbed';
    public static string $templateTransformMode = 'none';

    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-test');
    }

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        if(static::$templateTransformMode == 'none') {
            return $data;
        }

        $transformMethodName = static::$templateTransformMode;

        return [
            'text' => $data['text'],
            'formatted' => Str::$transformMethodName($data['text']),
            'form' => $isForm,
        ];
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function updateContent(array $data = []): array
    {
    }
}
