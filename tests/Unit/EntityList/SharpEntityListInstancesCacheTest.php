<?php

use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\Paginator;

beforeEach(function () {
    $this->people = [
        new Person(['id' => fake()->unique()->randomNumber(), 'name' => fake()->name]),
        new Person(['id' => fake()->unique()->randomNumber(), 'name' => fake()->name]),
    ];
});

it('caches array of entities at transform stage', function () {
    $list = new class($this->people) extends FakeSharpEntityList
    {
        public function __construct(private readonly array $people)
        {
        }

        public function getListData(): array|Arrayable
        {
            return $this->transform($this->people);
        }
    };

    $list->data();

    expect(sharp()->context()->findListInstance($this->people[0]->id))
        ->name
        ->toEqual($this->people[0]->name);
});

it('caches collection of entities at transform stage', function () {
    $list = new class($this->people) extends FakeSharpEntityList
    {
        public function __construct(private readonly array $people)
        {
        }

        public function getListData(): array|Arrayable
        {
            return $this->transform(collect($this->people));
        }
    };

    $list->data();

    expect(sharp()->context()->findListInstance($this->people[0]->id))
        ->name
        ->toEqual($this->people[0]->name);
});

it('caches paginated entities at transform stage', function () {
    $list = new class($this->people) extends FakeSharpEntityList
    {
        public function __construct(private readonly array $people)
        {
        }

        public function getListData(): array|Arrayable
        {
            return $this->transform(new Paginator($this->people, 2));
        }
    };

    $list->data();

    expect(sharp()->context()->findListInstance($this->people[0]->id))
        ->name
        ->toEqual($this->people[0]->name);
});

it('uses configured instance id to check id', function () {
    $list = new class($this->people) extends FakeSharpEntityList
    {
        private array $people;

        public function __construct(array $people)
        {
            $this->people = collect($people)
                ->map(function ($person) {
                    $person->key = $person->id;
                    unset($person->id);

                    return $person;
                })
                ->all();
        }

        public function buildListConfig(): void
        {
            $this->configureInstanceIdAttribute('key');
        }

        public function getListData(): array|Arrayable
        {
            return $this->transform($this->people);
        }
    };

    $list->buildListConfig();
    $list->data();

    expect(sharp()->context()->findListInstance($this->people[0]->key))
        ->name
        ->toEqual($this->people[0]->name);
});

it('callback is called in case of missing entity', function () {
    $list = new FakeSharpEntityList();

    $list->data();

    expect(sharp()->context()->findListInstance(
            12,
            fn ($id) => $id == 12 ? new Person(['id' => 12, 'name' => 'test']) : null
        ))
        ->name
        ->toEqual('test');
});
