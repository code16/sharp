<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class EntityListInstanceCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_info/1')
            ->assertOk()
            ->assertJson([
                'action' => 'info',
                'message' => 'ok',
            ]);
    }

    /** @test */
    public function we_can_call_a_link_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_link/1')
            ->assertStatus(200)
            ->assertJson([
                'action' => 'link',
                'link' => '/link/out',
            ]);
    }

    /** @test */
    public function we_can_call_a_refresh_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $json = $this->json('post', '/sharp/api/list/person/command/instance_refresh/1')
            ->assertStatus(200)
            ->assertJson([
                'action' => 'refresh',
                'items' => [
                    [
                        'id' => 1,
                        'name' => 'John <b>Wayne</b>',
                        'age' => 22,
                    ],
                ],
            ])
            ->decodeResponseJson();

        $this->assertCount(1, $json['items']);
    }

    /** @test */
    public function we_cant_call_an_unauthorized_instance_command()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/command/instance_unauthorized_odd_id/1')
            ->assertStatus(403);

        $this->json('post', '/sharp/api/list/person/command/instance_unauthorized_odd_id/2')
            ->assertStatus(200);
    }

    /** @test */
    public function we_can_get_the_form_of_the_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/instance_form/1/form')
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
    public function we_can_configure_a_global_message_on_an_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/instance_global_message/1/form')
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
            ]);
    }

    /** @test */
    public function we_can_get_a_localized_form_of_the_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/instance_form_localized/1/form')
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
                'data' => null,
            ]);
    }

    /** @test */
    public function we_can_initialize_form_data_in_an_instance_command()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this
            ->getJson('/sharp/api/list/person/command/instance_with_init_data/25/form')
            ->assertOk()
            ->assertJsonFragment([
                'data' => [
                    'name' => 'John Wayne [25]',
                ],
            ]);
    }

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);
        
        app(SharpEntityManager::class)
            ->entityFor('person')
            ->setList(EntityListInstanceCommandPersonSharpEntityList::class);
    }
}

class EntityListInstanceCommandPersonSharpEntityList extends PersonSharpEntityList
{
    public function getInstanceCommands(): ?array
    {
        return [
            'instance_info' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute($instanceId, array $params = []): array
                {
                    return $this->info('ok');
                }
            },
            'instance_refresh' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute($instanceId, array $params = []): array
                {
                    return $this->refresh(1);
                }
            },
            'instance_link' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function execute($instanceId, array $params = []): array
                {
                    return $this->link('/link/out');
                }
            },
            'instance_unauthorized_odd_id' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function authorizeFor($instanceId): bool
                {
                    return $instanceId % 2 == 0;
                }

                public function execute($instanceId, array $params = []): array
                {
                    return $this->reload();
                }
            },
            'instance_form' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                public function execute($instanceId, array $data = []): array
                {
                }
            },
            'instance_form_localized' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                public function execute($instanceId, array $data = []): array
                {
                }

                public function getDataLocalizations(): array
                {
                    return ['fr', 'en', 'it'];
                }
            },
            'instance_global_message' => new class() extends InstanceCommand
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

                public function execute($instanceId, array $data = []): array
                {
                }
            },
            'instance_with_init_data' => new class() extends InstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                protected function initialData($instanceId): array
                {
                    return [
                        'name' => "John Wayne [$instanceId]",
                        'age' => 32,
                    ];
                }

                public function execute($instanceId, array $data = []): array
                {
                }
            },
        ];
    }
}
