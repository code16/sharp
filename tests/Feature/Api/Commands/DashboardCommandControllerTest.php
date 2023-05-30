<?php

namespace Code16\Sharp\Tests\Feature\Api\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\SharpDashboard;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class DashboardCommandControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_dashboard_command()
    {
        $this->buildTheWorld();

        $this->postJson('/sharp/api/dashboard/personal_dashboard/command/dashboard_info')
            ->assertOk()
            ->assertJson([
                'action' => 'info',
                'message' => 'ok',
            ]);
    }

    /** @test */
    public function we_can_get_form_and_initialize_form_data_in_an_dashboard_command()
    {
        $this->buildTheWorld();

        $this->getJson('/sharp/api/dashboard/personal_dashboard/command/dashboard_form/form')
            ->assertOk()
            ->assertJson([
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
        parent::buildTheWorld(false);

        app(SharpEntityManager::class)
            ->entityFor('personal_dashboard')
            ->setView(EntityCommandTestSharpDashboard::class);
    }
}

class EntityCommandTestSharpDashboard extends SharpDashboard
{
    public function getDashboardCommands(): ?array
    {
        return [
            'dashboard_info' => new class() extends DashboardCommand
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
            'dashboard_form' => new class() extends DashboardCommand
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
