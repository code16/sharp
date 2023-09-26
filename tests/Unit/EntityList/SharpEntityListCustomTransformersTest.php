<?php

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

it('returns an array version of a model collection', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return $this
                ->transform(collect([
                    new Person(['name' => 'Marie Curie']),
                    new Person(['name' => 'Niels Bohr']),
                ]));
        }
    };

    expect($list->getListData())->toEqual([
        ['name' => 'Marie Curie'],
        ['name' => 'Niels Bohr'],
    ]);
});

it('handles paginated models', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return $this
                ->transform(
                    new LengthAwarePaginator(
                        items: [
                            new Person(['name' => 'Marie Curie']),
                            new Person(['name' => 'Niels Bohr']),
                        ],
                        total: 20,
                        perPage: 2,
                        currentPage: 1
                    )
                );
        }
    };

    expect($list->getListData())->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($list->getListData()->items())->toEqual([
            ['name' => 'Marie Curie'],
            ['name' => 'Niels Bohr'],
        ]);
});

it('handles the relation separator', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildList($fields): void
        {
            $fields
                ->addField(EntityListField::make('name'))
                ->addField(EntityListField::make('partner:name'));
        }

        public function getListData(): array|Arrayable
        {
            $person = new Person(['id' => 1, 'name' => 'Marie Curie']);
            $person->setRelation('partner', new Person(['id' => 2, 'name' => 'Pierre Curie']));

            return $this->transform([$person]);
        }
    };

    expect($list->getListData()[0])
        ->toHaveKey('name', 'Marie Curie')
        ->toHaveKey('partner:name', 'Pierre Curie');
});

it('allows to define a custom transformer as a closure', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return $this
                ->setCustomTransformer('name', fn ($name) => str($name)->upper())
                ->transform([
                    ['id' => 1, 'name' => 'Marie Curie'],
                    ['id' => 2, 'name' => 'Niels Bohr'],
                ]);
        }
    };

    expect($list->getListData())->toEqual([
        ['id' => 1, 'name' => 'MARIE CURIE'],
        ['id' => 2, 'name' => 'NIELS BOHR'],
    ]);
});

it('allows to define a custom transformer as an instance', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return $this
                ->setCustomTransformer('name', new class implements SharpAttributeTransformer {
                    public function apply($value, $instance = null, $attribute = null)
                    {
                        return str($value)->upper();
                    }
                })
                ->transform([
                    ['id' => 1, 'name' => 'Marie Curie'],
                    ['id' => 2, 'name' => 'Niels Bohr'],
                ]);
        }
    };

    expect($list->getListData())->toEqual([
        ['id' => 1, 'name' => 'MARIE CURIE'],
        ['id' => 2, 'name' => 'NIELS BOHR'],
    ]);
});

it('handles the relation separator with a custom transformer', function () {
    $list = new class extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name'))
                ->addField(EntityListField::make('partner:name'));
        }

        public function getListData(): array|Arrayable
        {
            $marie = new Person(['id' => 1, 'name' => 'Marie Curie']);
            $pierre = new Person(['id' => 2, 'name' => 'Pierre Curie']);
            $marie->setRelation('partner', $pierre);

            return $this
                ->setCustomTransformer('name', fn ($name) => strtoupper($name))
                ->setCustomTransformer('partner:name', function ($name, $user) {
                    return $user
                        ? 'Partner: ' . $name
                        : '';
                })
                ->transform([$marie, $pierre]);
        }
    };

    expect($list->getListData()[0])
        ->toHaveKey('name', 'MARIE CURIE')
        ->toHaveKey('partner:name', 'Partner: Pierre Curie');
});