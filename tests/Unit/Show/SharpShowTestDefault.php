<?php

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Tests\Unit\Show\Fakes\FakeSharpShow;
use Code16\Sharp\Tests\Unit\Show\Fakes\FakeSharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;

it('allows to add an EEL to the layout', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowEntityListField::make('entityList', 'entityKey')
                    ->setLabel('Test'),
            );
        }

        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addEntityListSection('entityList');
        }
    };

    expect($sharpShow->showLayout())
        ->toEqual(
            [
                'sections' => [
                    [
                        'collapsable' => false,
                        'title' => '',
                        'columns' => [
                            [
                                'size' => 12,
                                'fields' => [
                                    [
                                        [
                                            'key' => 'entityList',
                                            'size' => 12,
                                            'sizeXS' => 12,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'key' => null,
                    ],
                ],
            ]
        );
});

it('allows to declare a collapsable section', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowTextField::make('test')
                    ->setLabel('Test'),
            );
        }

        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addSection('test', function (ShowLayoutSection $section) {
                $section->setCollapsable()
                    ->addColumn(12, function (ShowLayoutColumn $column) {
                        $column->withField('test');
                    });
            });
        }
    };

    expect($sharpShow->showLayout())
        ->toEqual([
            'sections' => [
                [
                    'collapsable' => true,
                    'title' => 'test',
                    'columns' => [
                        [
                            'size' => 12,
                            'fields' => [
                                [
                                    [
                                        'key' => 'test',
                                        'size' => 12,
                                        'sizeXS' => 12,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'key' => null,
                ],
            ],
        ]);
});

it('allows to declare a collapsable EEL with a boolean', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowEntityListField::make('entityList', 'entityKey')
                    ->setLabel('Test'),
            );
        }

        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addEntityListSection('entityList', true);
        }
    };

    expect($sharpShow->showLayout()['sections'][0]['collapsable'])->toBeTrue();
});

it('allows to define a custom key to a section', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowTextField::make('test'),
            );
        }

        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout->addSection('test', function (ShowLayoutSection $section) {
                $section
                    ->setKey('my-section')
                    ->addColumn(12, function (ShowLayoutColumn $column) {
                        $column->withField('test');
                    });
            });
        }
    };

    expect($sharpShow->showLayout()['sections'][0]['key'])->toEqual('my-section');
});

it('allows to declare a multiformAttribute', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowConfig(): void
        {
            $this->configureMultiformAttribute('role');
        }
    };

    $sharpShow->buildShowConfig();

    expect($sharpShow->showConfig(1))
        ->toHaveKey('multiformAttribute', 'role');
});

it('allows to set an edit button label', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowConfig(): void
        {
            $this->configureEditButtonLabel('Edit post');
        }
    };
    
    $sharpShow->buildShowConfig();
    
    expect($sharpShow->showConfig(1))
        ->toHaveKey('editButtonLabel', 'Edit post');
});

it('allows to declare a page alert', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert');
        }
    };

    expect($sharpShow->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => PageAlertLevel::Info,
        ]);
});

it('allow to declare a simple page title field', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowConfig(): void
        {
            $this->configurePageTitleAttribute('title');
        }

        public function find($id): array
        {
            return [
                'title' => 'Some title',
            ];
        }
    };

    $sharpShow->buildShowConfig();

    expect($sharpShow->showConfig(1))
        ->toHaveKey('titleAttribute', 'title')
        ->and($sharpShow->fields()['title'])
        ->toEqual(SharpShowTextField::make('title')->toArray())
        ->and($sharpShow->instance(1))
        ->toHaveKey('title', 'Some title');
});

it('allow to declare a localized page title field', function () {
    $sharpShow = new class() extends FakeSharpShow
    {
        public function buildShowConfig(): void
        {
            $this->configurePageTitleAttribute('title', localized: true);
        }

        public function find($id): array
        {
            return [
                'title' => ['en' => 'Some title', 'fr' => 'Un titre'],
            ];
        }
    };

    $sharpShow->buildShowConfig();

    expect($sharpShow->fields()['title'])
        ->toEqual(SharpShowTextField::make('title')->setLocalized()->toArray())
        ->and($sharpShow->instance(1))
        ->toHaveKey('title', ['en' => 'Some title', 'fr' => 'Un titre']);
});

it('returns isSingle in config for single shows', function () {
    expect((new FakeSharpSingleShow())->showConfig(null))
        ->toHaveKey('isSingle', true);
});

it('allows to configure show instance command in sections', function () {
    $show = new class() extends FakeSharpShow
    {
        public function getInstanceCommands(): ?array
        {
            return [
                'cmd1' => new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'test';
                    }

                    public function execute(mixed $instanceId, array $data = []): array
                    {
                        return [];
                    }
                },
                'my-section' => [
                    'cmd2' => new class() extends InstanceCommand
                    {
                        public function label(): ?string
                        {
                            return 'test-2';
                        }

                        public function execute(mixed $instanceId, array $data = []): array
                        {
                            return [];
                        }
                    },
                ],
            ];
        }
    };

    $show->buildShowConfig();

    expect($show->showConfig(1)['commands']['instance'][0][0]['key'])->toEqual('cmd1')
        ->and($show->showConfig(1)['commands']['my-section'][0][0]['key'])->toEqual('cmd2');
});
