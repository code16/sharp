<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\EntityListEntities;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Tests\Fixtures\Entities\PersonChemistEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonPhysicistEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonUnknownEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('allows to reorder instances', function () {
    $this->withoutExceptionHandling();

    $list = new class() extends PersonList
    {
        public array $reorderedInstances = [];

        public function buildListConfig(): void
        {
            $this->configureReorderable(
                new class($this->reorderedInstances) implements ReorderHandler
                {
                    public function __construct(public array &$reorderedInstances) {}

                    public function reorder(array $ids): void
                    {
                        $this->reorderedInstances = $ids;
                    }
                }
            );
        }
    };

    fakeListFor('person', $list);

    $this
        ->postJson(
            route('code16.sharp.api.list.reorder', 'person'),
            ['instances' => [3, 2, 1]]
        )
        ->assertOk();

    expect($list->reorderedInstances)->toEqual([3, 2, 1]);
});

it('allows to delete an instance in the entity list if delete method is implemented', function () {
    $list = new class() extends PersonList
    {
        public ?int $deletedInstance = null;

        public function delete($id): void
        {
            $this->deletedInstance = $id;
        }
    };

    fakeListFor('person', $list);

    $idToDelete = rand(1, 10);

    $this->deleteJson(route('code16.sharp.api.list.delete', ['person', $idToDelete]))
        ->assertOk();

    expect($list->deletedInstance)->toEqual($idToDelete);
});

it('delegates deletion to the show page if it exists', function () {
    $this->withoutExceptionHandling();

    fakeListFor('person', new PersonList());

    $show = new class() extends PersonShow
    {
        public ?int $deletedInstance = null;

        public function delete($id): void
        {
            $this->deletedInstance = $id;
        }
    };
    fakeShowFor('person', $show);

    $idToDelete = rand(1, 10);

    $this->deleteJson(route('code16.sharp.api.list.delete', ['person', $idToDelete]))
        ->assertOk();

    expect($show->deletedInstance)->toEqual($idToDelete);
});

it('checks if the entity list allows deletion', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function delete(mixed $id): void
        {
            throw new Exception('Should not be called');
        }
    });

    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function delete($user, $instanceId): bool
        {
            return false;
        }
    });

    $this->deleteJson(route('code16.sharp.api.list.delete', ['person', 1]))
        ->assertForbidden();
});

it('throws an exception if delete is not implemented and there is no show', function () {
    fakeShowFor('person', null);

    $this->deleteJson(route('code16.sharp.api.list.delete', ['person', 1]))
        ->assertStatus(500);
});

it('gets list data as JSON in an EEL case', function () {
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

    $this
        ->getJson('/sharp/api/root/list/person', headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.0', fn (AssertableJson $json) => $json->where('id', 1)
                ->where('name', 'Marie Curie')
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json->where('id', 2)
                ->where('name', 'Niels Bohr')
                ->etc()
            )
            ->etc()
        );
});

it('sets appropriate `_meta` for each item linking to a show in an EEL case', function () {
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

    $this
        ->getJson('/sharp/api/root/list/person', headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.0', fn (AssertableJson $json) => $json
                ->where('_meta.url', route('code16.sharp.show.show', [
                    'parentUri' => 's-list/person/s-show/person/1',
                    'entityKey' => 'person',
                    'instanceId' => 1,
                ]))
                ->where('_meta.authorizations.view', true)
                ->where('_meta.authorizations.delete', true)
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json->where('id', 2)
                ->where('_meta.url', route('code16.sharp.show.show', [
                    'parentUri' => 's-list/person/s-show/person/1',
                    'entityKey' => 'person',
                    'instanceId' => 2,
                ]))
                ->where('_meta.authorizations.view', true)
                ->where('_meta.authorizations.delete', true)
                ->etc()
            )
            ->etc()
        );
});

it('sets appropriate `_meta` for each item linking to a form in an EEL case', function () {
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

    fakeShowFor('person', null);

    $this
        ->getJson('/sharp/api/root/list/person', headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.0', fn (AssertableJson $json) => $json
                ->where('_meta.url', route('code16.sharp.form.edit', [
                    'parentUri' => 's-list/person/s-show/person/1',
                    'entityKey' => 'person',
                    'instanceId' => 1,
                ]))
                ->where('_meta.authorizations.view', true)
                ->where('_meta.authorizations.delete', true)
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json->where('id', 2)
                ->where('_meta.url', route('code16.sharp.form.edit', [
                    'parentUri' => 's-list/person/s-show/person/1',
                    'entityKey' => 'person',
                    'instanceId' => 2,
                ]))
                ->where('_meta.authorizations.view', true)
                ->where('_meta.authorizations.delete', true)
                ->etc()
            )
            ->etc()
        );
});

it('sets appropriate `_meta` for with entities map', function () {
    sharp()->config()->declareEntity(PersonChemistEntity::class);
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);
    sharp()->config()->declareEntity(PersonUnknownEntity::class);

    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'chemist'],
                ['id' => 2, 'name' => 'Rosalind Franklin', 'job' => 'physicist'],
                ['id' => 3, 'name' => 'James Bond', 'job' => 'unknown'],
            ];
        }

        public function buildListConfig(): void
        {
            $this->configureEntityMap(
                attribute: 'job',
                entities: EntityListEntities::make()
                    ->addEntity('chemist', PersonChemistEntity::class, icon: 'testicon-car')
                    ->addEntity('physicist', PersonPhysicistEntity::class)
                    ->addEntity('unknown', PersonUnknownEntity::class),
            );
        }
    });

    $this
        ->getJson('/sharp/api/root/list/person', headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('data', 3)
            ->where('data.0._meta.url', route('code16.sharp.form.edit', [
                'parentUri' => 's-list/person/s-show/person/1',
                'entityKey' => 'person-chemist',
                'instanceId' => 1,
            ]))
            ->where('data.1._meta.url', route('code16.sharp.show.show', [
                'parentUri' => 's-list/person/s-show/person/1',
                'entityKey' => 'person-physicist',
                'instanceId' => 2,
            ]))
            ->whereNull('data.2._meta.url')
            ->etc()
        );
});

it('gets paginated data if wanted as JSON in an EEL case', function () {
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

    $this
        ->getJson('/sharp/api/root/list/person', headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.0', fn (AssertableJson $json) => $json->where('id', 1)
                ->where('name', 'Marie Curie')
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json->where('id', 2)
                ->where('name', 'Niels Bohr')
                ->etc()
            )
            ->has('meta', fn (AssertableJson $json) => $json->where('current_page', 1)
                ->where('per_page', 2)
                ->where('total', 20)
                ->where('from', 1)
                ->where('to', 2)
                ->where('last_page', 10)
                ->where('first_page_url', '/?page=1')
                ->where('last_page_url', '/?page=10')
                ->etc()
            )
            ->etc()
        );
});

it('get entities if configured', function () {
    $this->withoutExceptionHandling();

    sharp()->config()->declareEntity(PersonChemistEntity::class);
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);
    sharp()->config()->declareEntity(PersonUnknownEntity::class);

    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie', 'job' => 'chemist'],
                ['id' => 2, 'name' => 'Rosalind Franklin', 'job' => 'physicist'],
                ['id' => 3, 'name' => 'James Bond', 'job' => 'unknown'],
            ];
        }

        public function buildListConfig(): void
        {
            $this->configureEntityMap(
                attribute: 'job',
                entities: EntityListEntities::make()
                    ->addEntity('chemist', PersonChemistEntity::class, icon: 'testicon-car')
                    ->addEntity('physicist', PersonPhysicistEntity::class)
                    ->addEntity('unknown', PersonUnknownEntity::class),
            );
        }
    });

    $this
        ->getJson('/sharp/api/root/list/person', headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('data', 3)
            ->has('entities.0', fn (AssertableJson $json) => $json
                ->where('key', 'chemist')
                ->where('entityKey', 'person-chemist')
                ->where('label', 'Chemist')
                ->where('icon.name', 'testicon-car')
                ->where('formCreateUrl', route('code16.sharp.form.create', ['parentUri' => 's-list/person/s-show/person/1', 'entityKey' => 'person-chemist']))
                ->etc()
            )
            ->has('entities.1', fn (AssertableJson $json) => $json
                ->where('key', 'physicist')
                ->where('entityKey', 'person-physicist')
                ->where('label', 'Physicist')
                ->where('formCreateUrl', route('code16.sharp.form.create', ['parentUri' => 's-list/person/s-show/person/1', 'entityKey' => 'person-physicist']))
                ->etc()
            )
            ->has('entities.2', fn (AssertableJson $json) => $json
                ->where('key', 'unknown')
                ->where('entityKey', 'person-unknown')
                ->where('label', 'Unknown')
                ->where('formCreateUrl', route('code16.sharp.form.create', ['parentUri' => 's-list/person/s-show/person/1', 'entityKey' => 'person-unknown']))
                ->etc()
            )
            ->etc()
        );
});
