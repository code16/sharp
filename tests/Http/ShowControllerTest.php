<?php

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonSingleShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('gets formatted show data for an instance', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function find($id): array
        {
            return $this->transform([
                'name' => 'James Clerk Maxwell',
            ]);
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.data.name', ['text' => 'James Clerk Maxwell'])
            ->where('show.title', 'person')
        );
});

it('gets formatted show data even without data transformation', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function find($id): array
        {
            return [
                'name' => 'James Clerk Maxwell',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.data.name', ['text' => 'James Clerk Maxwell'])
        );
});

it('filters out data which is not a field', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(SharpShowTextField::make('name'));
        }

        public function find($id): array
        {
            return [
                'name' => 'James Clerk Maxwell',
                'age' => 22,
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('show.data.name')
            ->missing('show.data.age')
        );
});

it('gets attribute for entity state if defined', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(SharpShowTextField::make('name'));
        }

        public function buildShowConfig(): void
        {
            $this->configureEntityState('status', new class() extends EntityState
            {
                protected function buildStates(): void {}

                protected function updateState($instanceId, string $stateId): array
                {
                    return [];
                }
            });
        }

        public function find($id): array
        {
            return [
                'name' => 'Marie Curie',
                'status' => 'dead',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('show.data.name')
            ->where('show.data.status', 'dead')
        );
});

it('returns configured show fields', function () {
    $this->withoutExceptionHandling();
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(SharpShowTextField::make('name'))
                ->addField(SharpShowPictureField::make('picture'));
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('show.fields.name', fn (Assert $page) => $page
                ->where('type', 'text')
                ->etc()
            )
            ->has('show.fields.picture', fn (Assert $page) => $page
                ->where('type', 'picture')
                ->etc()
            )
        );
});

it('returns configured show layout', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(SharpShowTextField::make('name'))
                ->addField(SharpShowPictureField::make('picture'));
        }

        public function buildShowLayout(ShowLayout $showLayout): void
        {
            $showLayout
                ->addSection('test', function (ShowLayoutSection $section) {
                    return $section->addColumn(6, function (ShowLayoutColumn $column) {
                        return $column->withField('name')
                            ->withField('picture');
                    });
                });
        }
    });

    $this->withoutExceptionHandling();

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('show.layout.sections.0.columns.0', fn (Assert $page) => $page
                ->where('size', 6)
                ->where('fields.0.0.key', 'name')
                ->where('fields.1.0.key', 'picture')
                ->etc()
            )
        );
});

it('returns show configuration', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configureBreadcrumbCustomLabelAttribute('name');
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.config.breadcrumbAttribute', 'name')
        );
});

it('gets show data for an instance in a single show case', function () {
    sharp()->config()->declareEntity(SinglePersonEntity::class);

    fakeShowFor('single-person', new class() extends PersonSingleShow
    {
        public function findSingle(): array
        {
            return [
                'name' => 'Ernest Rutherford',
            ];
        }
    });

    $this->get('/sharp/s-show/single-person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.data.name.text', 'Ernest Rutherford')
        );
});

it('allows instance deletion from the show', function () {
    $personShow = new class() extends PersonShow
    {
        public bool $wasDeleted = false;

        public function delete($id): void
        {
            $this->wasDeleted = true;
        }
    };

    fakeShowFor('person', $personShow);

    $this->delete('/sharp/s-list/person/s-show/person/1')
        ->assertRedirect('/sharp/s-list/person');

    expect($personShow->wasDeleted)->toBeTrue();
});

it('disallows instance deletion without authorization', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['delete']);

    $this->delete('/sharp/s-list/person/s-show/person/1')
        ->assertForbidden();
});

it('returns commands authorization in config', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function getInstanceCommands(): array
        {
            return [
                new class() extends InstanceCommand
                {
                    public function label(): ?string
                    {
                        return 'command';
                    }

                    public function execute(mixed $instanceId, array $data = []): array
                    {
                        return $this->info('ok');
                    }

                    public function authorizeFor(mixed $instanceId): bool
                    {
                        return $instanceId < 10;
                    }
                },
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.config.commands.instance.0.0.authorization', true)
        );

    $this->get('/sharp/s-list/person/s-show/person/11')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.config.commands.instance.0.0.authorization', false)
        );
});

it('allows to configure a page alert', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert')
                ->setButton('My button', 'https://example.com');
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'My page alert',
                'buttonLabel' => 'My button',
                'buttonUrl' => 'https://example.com',
            ])
            ->etc()
        );
});

it('allows to configure a page alert on a specific section', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->onSection('my-section')
                ->setMessage('My page alert')
                ->setButton('My button', 'https://example.com');
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'My page alert',
                'sectionKey' => 'my-section',
                'buttonLabel' => 'My button',
                'buttonUrl' => 'https://example.com',
            ])
            ->etc()
        );
});

it('allows to configure a page alert with a closure as content', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage(function (array $data) {
                    return 'Hello '.$data['name'];
                });
        }

        public function find($id): array
        {
            return [
                'id' => 1,
                'name' => 'Marie Curie',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'Hello Marie Curie',
            ])
            ->etc()
        );
});

it('passes through transformers to return show data for an instance', function () {
    $this->withoutExceptionHandling();

    fakeShowFor('person', new class() extends PersonShow
    {
        public function find($id): array
        {
            return $this
                ->setCustomTransformer('name', fn ($name) => strtoupper($name))
                ->transform([
                    'name' => 'James Clerk Maxwell',
                ]);
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.data.name', ['text' => 'JAMES CLERK MAXWELL'])
        );
});

it('allows to configure a title attribute', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configurePageTitleAttribute('name');
        }

        public function find($id): array
        {
            return $this
                ->transform([
                    'name' => 'James Clerk Maxwell',
                ]);
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.title', 'James Clerk Maxwell')
        );
});

it('allows to configure a localized title attribute', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configurePageTitleAttribute('job', localized: true);
        }

        public function find($id): array
        {
            return $this
                ->transform([
                    'job' => [
                        'fr' => 'Physicien',
                        'en' => 'Physicist',
                    ],
                ]);
        }

        public function getDataLocalizations(): array
        {
            return ['fr', 'en'];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.title', [
                'fr' => 'Physicien',
                'en' => 'Physicist',
            ])
        );
});
