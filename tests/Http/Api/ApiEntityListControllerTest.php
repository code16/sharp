<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Enums\NotificationLevel;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonForm;
use Code16\Sharp\Tests\Fixtures\Entities\PersonList;
use Code16\Sharp\Tests\Fixtures\Entities\PersonShow;
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

it('allows to reorder instances', function () {
    $this->withoutExceptionHandling();

    $list = new class extends PersonList {
        public array $reorderedInstances = [];

        public function buildListConfig(): void
        {
            $this->configureReorderable(
                new class($this->reorderedInstances) implements ReorderHandler {
                    public function __construct(public array &$reorderedInstances)
                    {
                    }

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
    $list = new class extends PersonList {
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

    $show = new class extends PersonShow {
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
    fakeListFor('person', new class extends PersonList {
        public function delete(mixed $id): void
        {
            throw new Exception('Should not be called');
        }
    });

    fakePolicyFor('person', new class extends SharpEntityPolicy {
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