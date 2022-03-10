<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\Commands\SingleInstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\PersonSharpShow;
use Code16\Sharp\Tests\Fixtures\PersonSharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ShowInstanceCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_instance_command_from_a_show()
    {
        $this->buildTheWorld();

        $this->postJson('/sharp/api/show/person/command/instance_info/1')
            ->assertStatus(200)
            ->assertJson([
                'action' => 'info',
                'message' => 'ok',
            ]);

        $this->postJson('/sharp/api/show/person/command/instance_info')
            ->assertStatus(404);
    }

    /** @test */
    public function we_can_call_an_info_instance_command_from_a_single_show()
    {
        $this->buildTheWorld(true);

        $this->postJson('/sharp/api/show/person/command/instance_info')
            ->assertStatus(200)
            ->assertJson([
                'action' => 'info',
                'message' => 'ok',
            ]);

        $this->postJson('/sharp/api/show/person/command/instance_info/1')
            ->assertStatus(404);
    }

    /** @test */
    public function we_can_get_form_and_initialize_form_data_in_an_instance_command_from_a_show()
    {
        $this->buildTheWorld();
        $this->withoutExceptionHandling();

        $this->getJson('/sharp/api/show/person/command/instance_with_init_data/25/form')
            ->assertOk()
            ->assertJsonFragment([
                'data' => [
                    'name' => 'John Wayne [25]',
                ],
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
    public function we_can_get_form_and_initialize_form_data_in_an_instance_command_from_a_single_show()
    {
        $this->buildTheWorld(true);
        $this->withoutExceptionHandling();

        $this->getJson('/sharp/api/show/person/command/instance_with_init_data/form')
            ->assertOk()
            ->assertJsonFragment([
                'data' => [
                    'name' => 'John Wayne',
                ],
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

    protected function buildTheWorld($singleShow = false)
    {
        parent::buildTheWorld($singleShow);

        $this->app['config']->set(
            'sharp.entities.person.show',
            $singleShow
                ? ShowInstanceCommandPersonSharpSingleShow::class
                : ShowInstanceCommandPersonSharpShow::class,
        );
    }
}

class ShowInstanceCommandPersonSharpShow extends PersonSharpShow
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

class ShowInstanceCommandPersonSharpSingleShow extends PersonSharpSingleShow
{
    public function getInstanceCommands(): ?array
    {
        return [
            'instance_info' => new class() extends SingleInstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function executeSingle(array $params = []): array
                {
                    return $this->info('ok');
                }
            },
            'instance_with_init_data' => new class() extends SingleInstanceCommand
            {
                public function label(): string
                {
                    return 'label';
                }

                public function buildFormFields(FieldsContainer $formFields): void
                {
                    $formFields->addField(SharpFormTextField::make('name'));
                }

                protected function initialSingleData(): array
                {
                    return [
                        'name' => 'John Wayne',
                        'age' => 32,
                    ];
                }

                public function executeSingle(array $data = []): array
                {
                }
            },
        ];
    }
}
