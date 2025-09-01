<?php

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;

it('returns list commands config of a dashboard', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        public function getDashboardCommands(): ?array
        {
            return [
                'dashboardCommand' => new class() extends DashboardCommand
                {
                    public function label(): string
                    {
                        return 'My Dashboard Command';
                    }

                    public function execute(array $data = []): array {}
                },
            ];
        }
    };

    $dashboard->buildDashboardConfig();

    expect($dashboard->dashboardConfig()['commands']['dashboard'][0][0])
        ->toEqual([
            'key' => 'dashboardCommand',
            'label' => 'My Dashboard Command',
            'type' => 'dashboard',
            'authorization' => true,
            'description' => null,
            'confirmation' => null,
            'hasForm' => false,
            'icon' => null,
        ]);
});

it('handles list section placed commands config of a dashboard', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        public function getDashboardCommands(): ?array
        {
            return [
                'section-1' => [
                    'dashboardSectionCommand' => new class() extends DashboardCommand
                    {
                        public function label(): string
                        {
                            return 'My Dashboard Command';
                        }

                        public function execute(array $data = []): array {}
                    },
                ],
                'dashboardCommand' => new class() extends DashboardCommand
                {
                    public function label(): string
                    {
                        return 'Another Dashboard Command';
                    }

                    public function execute(array $data = []): array {}
                },
            ];
        }
    };

    $dashboard->buildDashboardConfig();

    expect($dashboard->dashboardConfig()['commands']['section-1'][0][0]['key'])
        ->toEqual('dashboardSectionCommand')
        ->and($dashboard->dashboardConfig()['commands']['dashboard'][0][0]['key'])
        ->toEqual('dashboardCommand');
});

it('allows to define that the dashboard command has a form', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        public function getDashboardCommands(): ?array
        {
            return [
                'dashboardCommand' => new class() extends DashboardCommand
                {
                    public function label(): string
                    {
                        return 'My Dashboard Command';
                    }

                    public function buildFormFields($formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('message'));
                    }

                    public function execute(array $data = []): array {}
                },
            ];
        }
    };

    $dashboard->buildDashboardConfig();

    expect($dashboard->dashboardConfig()['commands'])
        ->toEqual([
            'dashboard' => [
                [
                    [
                        'key' => 'dashboardCommand',
                        'label' => 'My Dashboard Command',
                        'type' => 'dashboard',
                        'hasForm' => true,
                        'authorization' => true,
                        'confirmation' => null,
                        'description' => null,
                        'icon' => null,
                    ],
                ],
            ],
        ]);
});
