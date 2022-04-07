<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EntityListEntityCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_info')
            ->assertOk()
            ->assertJson([
                'action' => 'info',
                'message' => 'ok',
            ]);
    }

    /** @test */
    public function we_can_call_a_reload_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_reload')
            ->assertOk()
            ->assertJson([
                'action' => 'reload',
            ]);
    }

    /** @test */
    public function we_can_call_a_view_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_view')
            ->assertOk()
            ->assertJson([
                'action' => 'view',
            ]);
    }

    /** @test */
    public function we_can_call_a_refresh_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_refresh')
            ->assertOk()
            ->assertJson([
                'action' => 'refresh',
                'items' => [
                    [
                        'id' => 1,
                        'name' => 'John <b>Wayne</b>',
                        'age' => 22,
                    ], [
                        'id' => 2,
                        'name' => 'Mary <b>Wayne</b>',
                        'age' => 26,
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_can_call_a_form_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->json('post', '/sharp/api/list/person/command/entity_form', [
                'data' => ['name' => 'John'],
            ])
            ->assertOk();
    }

    /** @test */
    public function we_can_call_a_download_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $response = $this->json('post', '/sharp/api/list/person/command/entity_download')
            ->assertOk();

        $this->assertTrue(
            Str::contains(
                $response->headers->get('content-disposition'), 'account.pdf',
            ),
        );

        $this
            ->json('post', '/sharp/api/list/person/command/entity_download_no_disk')
            ->assertOk();
    }

    /** @test */
    public function we_can_call_a_streamDownload_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $response = $this
            ->json('post', '/sharp/api/list/person/command/entity_streamDownload')
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=UTF-8');

        $this->assertTrue(
            Str::contains(
                $response->headers->get('content-disposition'), 'stream.txt',
            ),
        );
    }

    /** @test */
    public function applicative_exception_returns_a_417_as_always()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/command/entity_exception')
            ->assertStatus(417)
            ->assertJson([
                'message' => 'error',
            ]);
    }

    /** @test */
    public function we_get_a_422_when_posting_invalid_data_for_a_command_with_a_form()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/command/entity_form')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.',
                    ],
                ],
            ]);
    }

    /** @test */
    public function we_get_the_full_query_in_an_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->json('post', '/sharp/api/list/person/command/entity_params', [
                'query' => ['sort' => 'name', 'dir' => 'desc'],
            ])
            ->assertOk()
            ->assertJson([
                'action' => 'info',
                'message' => 'namedesc',
            ]);
    }

    /** @test */
    public function we_cant_call_an_unauthorized_entity_command()
    {
        $this->buildTheWorld();

        $this
            ->json('post', '/sharp/api/list/person/command/entity_unauthorized')
            ->assertStatus(403);
    }

    /** @test */
    public function we_can_get_the_form_of_the_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/entity_form/form')
            ->assertOk()
            ->assertJsonFragment([
                'config' => null,
                'fields' => [
                    'name' => [
                        'key' => 'name',
                        'type' => 'text',
                        'inputType' => 'text',
                    ],
                ],
                'layout' => [
                    [['key' => 'name', 'size' => 12, 'sizeXS' => 12]],
                ],
            ]);
    }

    /** @test */
    public function we_can_configure_a_global_message_on_an_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/entity_global_message/form')
            ->assertOk()
            ->assertJsonFragment([
                'config' => [
                    'globalMessage' => [
                        'fieldKey' => 'global_message',
                        'alertLevel' => null,
                    ],
                ],
                'fields' => [
                    'global_message' => [
                        'key' => 'global_message',
                        'type' => 'html',
                        'emptyVisible' => false,
                        'template' => 'template',
                    ],
                    'name' => [
                        'key' => 'name',
                        'type' => 'text',
                        'inputType' => 'text',
                    ],
                ],
                'layout' => [
                    [['key' => 'name', 'size' => 12, 'sizeXS' => 12]],
                ],
                'data' => null,
            ]);
    }

    /** @test */
    public function we_can_get_a_localized_form_of_the_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/entity_form_localized/form')
            ->assertOk()
            ->assertJsonFragment([
                'config' => null,
                'fields' => [
                    'name' => [
                        'key' => 'name',
                        'type' => 'text',
                        'inputType' => 'text',
                    ],
                ],
                'layout' => [
                    [['key' => 'name', 'size' => 12, 'sizeXS' => 12]],
                ],
                'locales' => ['fr', 'en', 'it'],
            ]);
    }

    /** @test */
    public function we_can_initialize_form_data_in_an_entity_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/entity_with_init_data/form')
            ->assertOk()
            ->assertJsonFragment([
                'data' => [
                    'name' => 'John Wayne',
                ],
            ]);
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        $this->app['config']->set(
            'sharp.entities.person.list',
            EntityCommandTestPersonSharpEntityList::class,
        );
    }
}

class EntityCommandTestPersonSharpEntityList extends PersonSharpEntityList
{
    public function getEntityCommands(): ?array
    {
        return [
            'entity_info' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    return $this->info('ok');
                }
            },
            'entity_reload' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    return $this->reload();
                }
            },
            'entity_view' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    return $this->view('welcome');
                }
            },
            'entity_refresh' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    return $this->refresh([1, 2]);
                }
            },
            'entity_exception' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    throw new SharpApplicativeException('error');
                }
            },
            'entity_form' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                public function execute(array $data = []): array
                {
                    $this->validate($data, ['name' => 'required']);

                    return $this->reload();
                }
            },
            'entity_global_message' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildCommandConfig(): void
                {
                    $this->configurePageAlert('template', null, 'global_message');
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                public function execute(array $data = []): array
                {
                }
            },
            'entity_form_localized' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                public function execute(array $data = []): array
                {
                }

                public function getDataLocalizations(): array
                {
                    return ['fr', 'en', 'it'];
                }
            },
            'entity_download' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    Storage::fake('files');
                    UploadedFile::fake()->create('account.pdf', 100, 'application/pdf')->storeAs('pdf', 'account.pdf', ['disk' => 'files']);

                    return $this->download('pdf/account.pdf', 'account.pdf', 'files');
                }
            },
            'entity_streamDownload' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    return $this->streamDownload('content', 'stream.txt');
                }
            },
            'entity_download_no_disk' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    Storage::fake('local');
                    UploadedFile::fake()->create('account.pdf', 100)->storeAs('pdf', 'account.pdf');

                    return $this->download('pdf/account.pdf');
                }
            },
            'entity_unauthorized' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function authorize(): bool
                {
                    return false;
                }

                public function execute(array $data = []): array
                {
                    return $this->reload();
                }
            },
            'entity_params' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute(array $data = []): array
                {
                    return $this->info($this->queryParams->sortedBy().$this->queryParams->sortedDir());
                }
            },
            'entity_with_init_data' => new class() extends EntityCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                protected function initialData(): array
                {
                    return [
                        'name' => 'John Wayne',
                        'age' => 32,
                    ];
                }

                public function execute(array $data = []): array
                {
                }
            },
        ];
    }
}
