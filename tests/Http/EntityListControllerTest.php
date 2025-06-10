<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\EntityListEntities;
use Code16\Sharp\EntityList\Fields\EntityListBadgeField;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Enums\NotificationLevel;
use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Tests\Fixtures\Entities\PersonChemistEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonPhysicistEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('gets list data for an entity', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return $this->transform([
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ]);
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.data.0', fn (Assert $json) => $json
                ->where('id', 1)
                ->where('name', 'Marie Curie')
                ->etc()
            )
            ->has('entityList.data.1', fn (Assert $json) => $json
                ->where('id', 2)
                ->where('name', 'Niels Bohr')
                ->etc()
            )
            ->count('entityList.data', 2)
        );
});

it('gets paginated data if wanted', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            $items = [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ];

            return $this->transform(new LengthAwarePaginator($items, 20, 2, 1));
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.data.0', fn (Assert $json) => $json
                ->where('id', 1)
                ->where('name', 'Marie Curie')
                ->etc()
            )
            ->has('entityList.data.1', fn (Assert $json) => $json
                ->where('id', 2)
                ->where('name', 'Niels Bohr')
                ->etc()
            )
            ->count('entityList.data', 2)
            ->has('entityList.meta', fn (Assert $name) => $name
                ->where('current_page', 1)
                ->where('from', 1)
                ->where('to', 2)
                ->where('last_page', 10)
                ->where('per_page', 2)
                ->where('total', 20)
                ->etc()
            )
        );
});

it('allows to search for items', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            $items = [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
                ['id' => 3, 'name' => 'Pierre Curie'],
            ];

            if ($this->queryParams->hasSearch()) {
                $items = collect($items)
                    ->filter(function ($item) {
                        return str($item['name'])
                            ->contains($this->queryParams->searchWords(false));
                    })
                    ->values();
            }

            return $this->transform($items);
        }

        public function buildListConfig(): void
        {
            $this->configureSearchable();
        }
    });

    $this->get('/sharp/s-list/person?search=Curie')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.data.0', fn (Assert $json) => $json
                ->where('id', 1)
                ->where('name', 'Marie Curie')
                ->etc()
            )
            ->has('entityList.data.1', fn (Assert $json) => $json
                ->where('id', 3)
                ->where('name', 'Pierre Curie')
                ->etc()
            )
            ->count('entityList.data', 2)
        );
});

it('filters out data which is not displayed', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return $this->transform([
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'Physicist'],
            ]);
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->missing('entityList.data.list.items.0.job')
        );
});

it('gets containers and layout', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(
                    EntityListBadgeField::make('is_new')
                        ->setTooltip('This person is new')
                )
                ->addField(
                    EntityListField::make('name')
                        ->setLabel('Name')
                        ->setWidth(6)
                        ->setSortable()
                )
                ->addField(
                    EntityListField::make('job')
                        ->setLabel('Job')
                        ->setWidth(6)
                        ->hideOnSmallScreens()
                );
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.fields', 3)
            ->has('entityList.fields.0', fn (Assert $name) => $name
                ->where('key', 'is_new')
                ->where('type', 'badge')
                ->where('tooltip', 'This person is new')
                ->where('hideOnXS', false)
                ->where('sortable', false)
                ->etc()
            )
            ->has('entityList.fields.1', fn (Assert $name) => $name
                ->where('key', 'name')
                ->where('label', 'Name')
                ->where('width', '50%')
                ->where('hideOnXS', false)
                ->where('sortable', true)
                ->etc()
            )
            ->has('entityList.fields.2', fn (Assert $job) => $job
                ->where('key', 'job')
                ->where('label', 'Job')
                ->where('width', '50%')
                ->where('hideOnXS', true)
                ->etc()
            )
        );
});

it('gets config', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureSearchable()
                ->configureDefaultSort('name')
                ->configureDelete(hide: true);
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.config', fn (Assert $config) => $config
                ->where('searchable', true)
                ->where('defaultSort', 'name')
                ->where('deleteHidden', true)
                ->etc()
            )
        );
});

it('gets authorizations of each instance', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
                ['id' => 3, 'name' => 'Albert Einstein'],
            ];
        }
    });

    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function view($user, $instanceId): bool
        {
            return $instanceId != 3;
        }

        public function delete($user, $instanceId): bool
        {
            return $instanceId == 2;
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.authorizations', [
                'create' => true,
                'reorder' => true,
            ])
            ->where('entityList.data.0._meta.authorizations', [
                'view' => true,
                'delete' => false,
            ])
            ->where('entityList.data.1._meta.authorizations', [
                'view' => true,
                'delete' => true,
            ])
        );
});

it('gets multiforms if configured', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie', 'nobel' => 'yes'],
                ['id' => 2, 'name' => 'Rosalind Franklin', 'nobel' => 'nope'],
            ];
        }

        public function buildListConfig(): void
        {
            $this->configureMultiformAttribute('nobel');
        }
    });

    app(\Code16\Sharp\Utils\Entities\SharpEntityManager::class)
        ->entityFor('person')
        ->setMultiforms([
            'yes' => [PersonForm::class, 'With Nobel prize'],
            'nope' => [PersonForm::class, 'No Nobel prize'],
        ]);

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.entities', 2)
            ->has('entityList.entities.0', fn (Assert $config) => $config
                ->where('key', 'yes')
                ->where('entityKey', 'person:yes')
                ->where('label', 'With Nobel prize')
                // ->where('instances', [1])
                ->etc()
            )
            ->has('entityList.entities.1', fn (Assert $config) => $config
                ->where('key', 'nope')
                ->where('entityKey', 'person:nope')
                ->where('label', 'No Nobel prize')
                // ->where('instances', [2])
                ->etc()
            )
        );
});

it('get entities if configured', function () {
    $this->withoutExceptionHandling();

    sharp()->config()->declareEntity(PersonChemistEntity::class);
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);

    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'chemist'],
                ['id' => 2, 'name' => 'Rosalind Franklin', 'job' => 'physicist'],
            ];
        }

        public function buildListConfig(): void
        {
            $this->configureEntityMap(
                attribute: 'job',
                entities: EntityListEntities::make()
                    ->addEntity('chemist', PersonChemistEntity::class, icon: 'testicon-car')
                    ->addEntity('physicist', PersonPhysicistEntity::class),
            );
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.entities', 2)
            ->has('entityList.entities.0', fn (Assert $config) => $config
                ->where('key', 'chemist')
                ->where('entityKey', 'person-chemist')
                ->where('label', 'Chemist')
                ->where('icon.name', 'testicon-car')
                ->etc()
            )
            ->has('entityList.entities.1', fn (Assert $config) => $config
                ->where('key', 'physicist')
                ->where('entityKey', 'person-physicist')
                ->where('label', 'Physicist')
                ->etc()
            )
        );
});

it('handles notifications', function () {
    (new PersonForm())->notify('my title')
        ->setLevelSuccess()
        ->setDetail('body')
        ->setAutoHide(false);

    $this->withoutExceptionHandling();

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('notifications', 1)
            ->has('notifications.0', fn (Assert $config) => $config
                ->where('level', NotificationLevel::Success->value)
                ->where('title', 'my title')
                ->where('message', 'body')
                ->where('autoHide', false)
            )
        );

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('notifications', 0)
        );

    (new PersonForm())->notify('title1');
    (new PersonForm())->notify('title2');

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('notifications', 2)
        );
});

it('returns a 404 with invalid entity key', function () {
    $this->get('/sharp/s-list/not-a-valid-entity-key')
        ->assertStatus(404);
});

it('handles hasShowPage in config', function () {
    fakeShowFor('person', new PersonShow());

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.config', fn (Assert $config) => $config
                ->where('hasShowPage', true)
                ->etc()
            )
        );

    fakeShowFor('person', null);

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.config', fn (Assert $config) => $config
                ->where('hasShowPage', false)
                ->etc()
            )
        );
});

it('allows to configure a page alert', function () {
    $this->withoutExceptionHandling();

    fakeListFor('person', new class() extends PersonList
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert')
                ->setButton('My button', LinkToEntityList::make('person'));
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'My page alert',
                'buttonLabel' => 'My button',
                'buttonUrl' => LinkToEntityList::make('person')->renderAsUrl(),
            ])
            ->etc()
        );
});

it('allows to configure a page alert with a closure as content', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage(function (array $data) {
                    return 'There are '.count($data).' items.';
                });
        }

        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ];
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'There are 2 items.',
            ])
            ->etc()
        );
});
