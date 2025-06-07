<?php

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListBadgeField;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

it('gets fields with layout', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setWidth(.5)
            );
        }
    };

    expect($list->fields())->toEqual([
        [
            'type' => 'text',
            'key' => 'name',
            'label' => 'Name',
            'sortable' => false,
            'html' => true,
            'width' => '50%',
            'hideOnXS' => false,
        ],
    ]);
});

it('allows to add badge fields', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields->addField(
                EntityListBadgeField::make('is_new')
                    ->setTooltip('This person is new')
            );
        }
    };

    expect($list->fields())->toEqual([
        [
            'type' => 'badge',
            'key' => 'is_new',
            'label' => null,
            'sortable' => false,
            'width' => null,
            'hideOnXS' => false,
            'tooltip' => 'This person is new',
        ],
    ]);
});

it('allows to set fields width as a legacy 12-based grid', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidth(5))
                ->addField(EntityListField::make('age')->setWidth(7));
        }
    };

    expect(collect($list->fields())->pluck('width')->toArray())
        ->toEqual(['42%', '58%']);
});

it('allows to set fields width as a floats', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidth(.6))
                ->addField(EntityListField::make('age')->setWidth(.4));
        }
    };

    expect(collect($list->fields())->pluck('width')->toArray())
        ->toEqual(['60%', '40%']);
});

it('allows to set fields width as a percentage string', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidth('60'))
                ->addField(EntityListField::make('name')->setWidth('10%'))
                ->addField(EntityListField::make('age')->setWidth('30 %'));
        }
    };

    expect(collect($list->fields())->pluck('width')->toArray())
        ->toEqual(['60%', '10%', '30%']);
});

it('throws an exception on invalid values for fields width', function ($invalidWidth) {
    $this->expectException(SharpInvalidConfigException::class);

    $list = new class($invalidWidth) extends FakeSharpEntityList
    {
        public function __construct(private $invalidWidth) {}

        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidth($this->invalidWidth));
        }
    };

    $list->fields();
})->with(['foo', '101', '-1', -1, 101, 1.5, -.2]);

it('allows to hide a column on small screens', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidth(.4))
                ->addField(EntityListField::make('age')->setWidth(.6)->hideOnSmallScreens());
        }
    };

    expect($list->fields()[0])
        ->toMatchArray([
            'key' => 'name',
            'width' => '40%',
            'hideOnXS' => false,
        ])
        ->and($list->fields()[1])->toMatchArray([
            'key' => 'age',
            'width' => '60%',
            'hideOnXS' => true,
        ]);
});

it('allows to configure a column to fill left space', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidthFill())
                ->addField(EntityListField::make('age')->setWidthFill());
        }
    };

    expect($list->fields()[0])
        ->toHaveKey('width', 'fill')
        ->toHaveKey('hideOnXS', false)
        ->and($list->fields()[1])
        ->toHaveKey('width', 'fill')
        ->toHaveKey('hideOnXS', false);
});

it('returns list data', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getListData(): array
        {
            return [
                ['name' => 'John Wayne', 'age' => 22, 'job' => 'actor'],
                ['name' => 'Mary Wayne', 'age' => 26, 'job' => 'truck driver'],
            ];
        }

        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name'))
                ->addField(EntityListField::make('age'));
        }
    };

    expect($list->data()['items'])->toEqual([
        ['name' => 'John Wayne', 'age' => 22],
        ['name' => 'Mary Wayne', 'age' => 26],
    ]);
});

it('can return paginated data', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            $data = [
                ['name' => 'John Wayne', 'age' => 22, 'job' => 'actor'],
                ['name' => 'Mary Wayne', 'age' => 26, 'job' => 'truck driver'],
            ];

            return new LengthAwarePaginator($data, 10, 2, 1);
        }

        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name'))
                ->addField(EntityListField::make('age'));
        }
    };

    expect($list->data()['items'])
        ->toEqual([
            ['name' => 'John Wayne', 'age' => 22],
            ['name' => 'Mary Wayne', 'age' => 26],
        ])
        ->and($list->data()['meta'])
        ->toMatchArray([
            'current_page' => 1,
            'from' => 1,
            'to' => 2,
            'last_page' => 5,
            'per_page' => 2,
            'total' => 10,
        ]);
});

it('returns list config', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureSearchable()
                ->configureDefaultSort('name');
        }
    };

    $list->buildListConfig();

    expect($list->listConfig())->toEqual([
        'searchable' => true,
        'reorderable' => false,
        'hasShowPage' => false,
        'instanceIdAttribute' => 'id',
        'subEntityAttribute' => null,
        'defaultSort' => 'name',
        'defaultSortDir' => 'asc',
        'deleteHidden' => false,
        'deleteConfirmationText' => trans('sharp::show.delete_confirmation_text'),
        'filters' => null,
        'createButtonLabel' => null,
        'quickCreationForm' => false,
    ]);
});

it('allows to configure a page alert', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelDanger()
                ->setMessage('My page alert')
                ->setButton('My button', LinkToEntityList::make('person'));
        }
    };

    expect($list->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => PageAlertLevel::Danger,
            'buttonLabel' => 'My button',
            'buttonUrl' => LinkToEntityList::make('person')->renderAsUrl(),
        ]);
});

it('allows to configure the deletion action to disallow it', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureDelete(hide: true);
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['deleteHidden'])->toBeTrue();
});

it('allows to configure the deletion action confirmation text', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureDelete(confirmationText: 'ok?');
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['deleteHidden'])->toBeFalse()
        ->and($list->listConfig()['deleteConfirmationText'])->toEqual('ok?');
});

it('allows to configure a reorder handler', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureReorderable(new class() implements ReorderHandler
            {
                public function reorder(array $ids): void {}
            });
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['reorderable'])->toBeTrue()
        ->and($list->reorderHandler())->toBeInstanceOf(ReorderHandler::class);
});

it('allows to disable a configured reorder handler', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureReorderable(new class() implements ReorderHandler
            {
                public function reorder(array $ids): void {}
            });
        }

        public function getListData(): array|Arrayable
        {
            $this->disableReorder();

            return [];
        }
    };

    $list->buildListConfig();
    $list->data();

    expect($list->listConfig()['reorderable'])->toBeFalse();
});

it('allows to configure a create button label', function () {
    $list = new class() extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureCreateButtonLabel('New post...');
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['createButtonLabel'])->toEqual('New post...');
});
