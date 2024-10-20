<?php

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\TestDashboard;
use Code16\Sharp\Utils\Fields\FieldsContainer;

beforeEach(function () {
    sharp()->config()->addEntity('dashboard', DashboardEntity::class);
    login();
});

it('allows to call an info dashboard command', function () {
    fakeShowFor('dashboard', new class extends TestDashboard
    {
        public function getDashboardCommands(): ?array
        {
            return [
                'info' => new class extends DashboardCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->info('ok');
                    }
                },
            ];
        }
    });

    $this->withoutExceptionHandling();

    $this->postJson(route('code16.sharp.api.dashboard.command', ['dashboard', 'info']))
        ->assertOk()
        ->assertJson([
            'action' => 'info',
            'message' => 'ok',
        ]);
});

it('allows to initialize form data in a dashboard command', function () {
    fakeShowFor('dashboard', new class extends TestDashboard
    {
        public function getDashboardCommands(): ?array
        {
            return [
                'command_with_init_data' => new class extends DashboardCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }
                    
                    public function buildCommandConfig(): void
                    {
                        $this->configureFormModalTitle(fn ($data) => "Edit {$data['name']}")
                            ->configureFormModalDescription('Custom description');
                    }
                    
                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name')->setLocalized());
                    }

                    public function execute(array $data = []): array
                    {
                        return $this->reload();
                    }

                    public function initialData(): array
                    {
                        return [
                            'name' => 'Marie Curie',
                        ];
                    }
                },
            ];
        }
    });

    $this
        ->getJson(route('code16.sharp.api.dashboard.command.form', ['dashboard', 'command_with_init_data']))
        ->assertOk()
        ->assertJsonFragment([
            'data' => [
                'name' => 'Marie Curie',
            ],
            'config' => [
                'title' => 'Edit Marie Curie',
                'description' => 'Custom description',
            ],
        ]);
});
