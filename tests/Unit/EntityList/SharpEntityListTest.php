<?php

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;

it('gets fields with layout', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setWidth(6)
            );
        }
    };

    expect($list->fields())->toEqual([
        [
            'key' => 'name',
            'label' => 'Name',
            'sortable' => false,
            'html' => true,
            'size' => 6,
            'sizeXS' => 6,
            'hideOnXS' => false
        ],
    ]);
});

it('allows to define layout for small screens', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidth(6)->setWidthOnSmallScreens(12))
                ->addField(EntityListField::make('age')->setWidth(6)->hideOnSmallScreens());
        }
    };

    expect($list->fields()[0])
        ->toEqual([
            'key' => 'name',
            'label' => '',
            'sortable' => false,
            'html' => true,
            'size' => 6,
            'sizeXS' => 12,
            'hideOnXS' => false
        ])
        ->and($list->fields()[1])->toEqual([
            'key' => 'age',
            'label' => '',
            'sortable' => false,
            'html' => true,
            'size' => 6,
            'sizeXS' => 6,
            'hideOnXS' => true
        ]);
});

it('allows to configure a column to fill left space', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildList(EntityListFieldsContainer $fields): void
        {
            $fields
                ->addField(EntityListField::make('name')->setWidthOnSmallScreens(4))
                ->addField(EntityListField::make('age'));
        }
    };

    expect($list->fields()[0])
        ->toHaveKey('size', 'fill')
        ->toHaveKey('sizeXS', 4)
        ->toHaveKey('hideOnXS', false)
        ->and($list->fields()[1])
        ->toHaveKey('size', 'fill')
        ->toHaveKey('sizeXS', 'fill')
        ->toHaveKey('hideOnXS', false);
});

it('returns list data', function() {
    $list = new class extends FakeSharpEntityList
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

    expect($list->data()['list'])->toEqual([
        'items' => [
            ['name' => 'John Wayne', 'age' => 22],
            ['name' => 'Mary Wayne', 'age' => 26],
        ]
    ]);
});

it('can return paginated data', function() {
    $list = new class extends FakeSharpEntityList
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

    expect($list->data()['list'])->toEqual([
        'items' => [
            ['name' => 'John Wayne', 'age' => 22],
            ['name' => 'Mary Wayne', 'age' => 26],
        ],
        'page' => 1,
        'pageSize' => 2,
        'totalCount' => 10,
    ]);
});

it('returns list config', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureSearchable()
                ->configurePaginated();
        }
    };

    $list->buildListConfig();

    expect($list->listConfig())->toEqual([
        'searchable' => true,
        'paginated' => true,
        'reorderable' => false,
        'hasShowPage' => false,
        'instanceIdAttribute' => 'id',
        'multiformAttribute' => null,
        'defaultSort' => null,
        'defaultSortDir' => null,
        'deleteHidden' => false,
        'deleteConfirmationText' => trans('sharp::show.delete_confirmation_text'),
    ]);
});

it('allows to configure a page alert', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelDanger()
                ->setMessage('My page alert');
        }
    };

    expect($list->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => \Code16\Sharp\Enums\PageAlertLevel::Danger->value,
        ]);
});

it('allows to configure the deletion action to disallow it', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureDelete(hide: true);
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['deleteHidden'])->toBeTrue();
});

it('allows to configure the deletion action confirmation text', function() {
    $list = new class extends FakeSharpEntityList
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
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configureReorderable(new class implements ReorderHandler
            {
                public function reorder(array $ids): void
                {
                }
            });
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['reorderable'])->toBeTrue()
        ->and($list->reorderHandler())->toBeInstanceOf(ReorderHandler::class);
});