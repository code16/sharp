<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonForm;
use Code16\Sharp\Tests\Fixtures\Entities\PersonList;
use \Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('gets list data for an entity', function () {
    fakeListFor('person', new class extends PersonList {
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
            ->where('entityList.data.list.items', [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ])
        );
});

it('gets paginated data is configured', function () {
    fakeListFor('person', new class extends PersonList {
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
            ->where('entityList.data.list.items', [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ])
            ->where('entityList.data.list.page', 1)
            ->where('entityList.data.list.totalCount', 20)
            ->where('entityList.data.list.pageSize', 2)
        );
});

it('allows to search for items', function () {
    fakeListFor('person', new class extends PersonList {
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
            ->where('entityList.data.list.items', [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 3, 'name' => 'Pierre Curie'],
            ])
        );
});

it('filters out data which is not displayed', function () {
    fakeListFor('person', new class extends PersonList {
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
    fakeListFor('person', new class extends PersonList {
        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer
                ->addField(
                    EntityListField::make('name')
                        ->setLabel('Name')
                        ->setWidth(6)
                        ->setWidthOnSmallScreensFill()
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
            ->has('entityList.containers', 2)
            ->has('entityList.containers.name', fn (Assert $name) => $name
                ->where('key', 'name')
                ->where('label', 'Name')
                ->where('sortable', true)
                ->etc()
            )
            ->has('entityList.containers.job', fn (Assert $job) => $job
                ->where('key', 'job')
                ->etc()
            )
            ->has('entityList.layout', 2)
            ->has('entityList.layout.0', fn (Assert $name) => $name
                ->where('key', 'name')
                ->where('size', '6')
                ->where('hideOnXS', false)
                ->where('sizeXS', 'fill')
            )
            ->has('entityList.layout.1', fn (Assert $job) => $job
                ->where('key', 'job')
                ->where('size', '6')
                ->where('hideOnXS', true)
                ->etc()
            )
        );
});

it('gets config', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class extends PersonList {
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
    fakeListFor('person', new class extends PersonList {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
                ['id' => 3, 'name' => 'Albert Einstein'],
            ];
        }
    });

    fakePolicyFor('person', new class extends SharpEntityPolicy {
        public function view($user, $instanceId): bool
        {
            return $instanceId != 3;
        }
        public function update($user, $instanceId): bool
        {
            return $instanceId == 1;
        }
        public function delete($user, $instanceId): bool
        {
            return $instanceId == 2;
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('entityList.authorizations', fn (Assert $config) => $config
                ->where('create', true)
                ->where('view', [1, 2])
                ->where('update', [1])
                ->where('delete', [2])
            )
        );
});

it('gets multiforms if configured', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class extends PersonList {
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
            ->has('entityList.forms', 2)
            ->has('entityList.forms.yes', fn (Assert $config) => $config
                ->where('label', 'With Nobel prize')
                ->where('instances', [1])
                ->etc()
            )
            ->has('entityList.forms.nope', fn (Assert $config) => $config
                ->where('label', 'No Nobel prize')
                ->where('instances', [2])
                ->etc()
            )
        );
});

it('allows to reorder instances', function () {
    $this->withoutExceptionHandling();
    fakeListFor('person', new class extends PersonList {
        public array $data = [
            ['id' => 1, 'name' => 'Marie Curie', 'order' => 1],
            ['id' => 2, 'name' => 'Rosalind Franklin', 'order' => 2],
            ['id' => 3, 'name' => 'Niels Bohr', 'order' => 3]
        ];

        public function getListData(): array|Arrayable
        {
            return collect($this->data)
                ->sortBy('order')
                ->values()
                ->all();
        }

        public function buildListConfig(): void
        {
            $this->configureReorderable(
                new class($this->data) implements ReorderHandler {
                    public function __construct(public array &$data)
                    {
                    }

                    public function reorder(array $ids): void
                    {
                        $this->data = collect($this->data)
                            ->map(fn ($item) => [
                                'id' => $item['id'],
                                'name' => $item['name'],
                                'order' => array_search($item['id'], $ids) + 1,
                            ])
                            ->values()
                            ->all();
                    }
                }
            );
        }
    });

    $this
        ->post(
            route('code16.sharp.list.reorder', 'person'),
            ['instances' => [3, 2, 1]]
        )
        ->assertRedirect(route('code16.sharp.list', 'person'));
});

//    /** @test */
//    public function we_can_get_notifications()
//    {
//        (new PersonSharpForm())->notify('title')
//            ->setLevelSuccess()
//            ->setDetail('body')
//            ->setAutoHide(false);
//
//        $this->json('get', '/sharp/api/list/person')
//            ->assertOk()
//            ->assertJson(['notifications' => [[
//                'level' => 'success',
//                'title' => 'title',
//                'message' => 'body',
//                'autoHide' => false,
//            ]]]);
//
//        $this->json('get', '/sharp/api/list/person')
//            ->assertOk()
//            ->assertJsonMissing(['alert']);
//
//        (new PersonSharpForm())->notify('title1');
//        (new PersonSharpForm())->notify('title2');
//
//        $this->json('get', '/sharp/api/list/person')
//            ->assertOk()
//            ->assertJson(['notifications' => [[
//                'title' => 'title1',
//            ], [
//                'title' => 'title2',
//            ]]]);
//    }
//
//    /** @test */
//    public function invalid_entity_key_is_returned_as_404()
//    {
//        $this->getJson('/sharp/api/list/notanvalidentity')
//            ->assertStatus(404);
//    }
//
//    /** @test */
//    public function list_config_contains_hasShowPage_is_relevant()
//    {
//        $this->getJson('/sharp/api/list/person')
//            ->assertOk()
//            ->assertJson(['config' => [
//                'hasShowPage' => true,
//            ]]);
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setShow(null);
//
//        $this->getJson('/sharp/api/list/person')
//            ->assertOk()
//            ->assertJson(['config' => [
//                'hasShowPage' => false,
//            ]]);
//    }
//
//    /** @test */
//    public function we_can_delete_an_instance_in_the_entity_list_if_delete_method_is_implemented()
//    {
//        $this->withoutExceptionHandling();
//        $this->buildTheWorld();
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setList(PersonSharpEntityListWithDeletion::class);
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setShow(PersonSharpShowWithoutDeletion::class);
//
//        $this->deleteJson('/sharp/api/list/person/1')
//            ->assertOk()
//            ->assertJson([
//                'ok' => true,
//            ]);
//    }
//
//    /** @test */
//    public function we_delegate_deletion_to_the_show_page_if_exists()
//    {
//        $this->withoutExceptionHandling();
//        $this->buildTheWorld();
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setList(PersonSharpEntityListWithoutDeletion::class);
//
//        $this->deleteJson('/sharp/api/list/person/1')
//            ->assertOk()
//            ->assertJson([
//                'ok' => true,
//            ]);
//    }
//
//    /** @test */
//    public function as_a_legacy_workaround_we_delegate_deletion_to_the_form_page_if_exists()
//    {
//        $this->withoutExceptionHandling();
//        $this->buildTheWorld();
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setList(PersonSharpEntityListWithoutDeletion::class);
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setShow(null);
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setForm(PersonSharpFormWithDeletion::class);
//
//        $this->deleteJson('/sharp/api/list/person/1')
//            ->assertOk()
//            ->assertJson([
//                'ok' => true,
//            ]);
//    }
//
//    /** @test */
//    public function we_can_not_delete_an_instance_in_the_entity_list_without_authorization()
//    {
//        $this->buildTheWorld();
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setList(PersonSharpEntityListWithDeletion::class);
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setProhibitedActions(['delete']);
//
//        $this->deleteJson('/sharp/api/list/person/1')
//            ->assertForbidden();
//    }
//
//    /** @test */
//    public function we_throw_an_exception_if_delete_is_not_implemented_and_there_is_no_show()
//    {
//        $this->buildTheWorld();
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setList(PersonSharpEntityListWithoutDeletion::class);
//
//        app(SharpEntityManager::class)
//            ->entityFor('person')
//            ->setShow(null);
//
//        $this->deleteJson('/sharp/api/list/person/1')
//            ->assertStatus(500);
//    }
//}
