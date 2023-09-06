<?php

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Tests\Unit\EntityList\Utils\FakeSharpEntityList;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;

it('gets containers', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer->addField(
                EntityListField::make('name')
                    ->setLabel('Name'),
            );
        }
    };

    expect($list->fields())->toEqual([
        'name' => [
            'key' => 'name',
            'label' => 'Name',
            'sortable' => false,
            'html' => true,
        ],
    ]);
});

it('returns layout', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer
                ->addField(EntityListField::make('name')->setWidth(6))
                ->addField(EntityListField::make('age')->setWidth(6));
        }
    };

    expect($list->listLayout())->toEqual([
        ['key' => 'name', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => false],
        ['key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => false]
    ]);
});

it('allows to define layout for small screens', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer
                ->addField(EntityListField::make('name')->setWidth(6)->setWidthOnSmallScreens(12))
                ->addField(EntityListField::make('age')->setWidth(6)->hideOnSmallScreens());
        }
    };

    expect($list->listLayout())->toEqual([
        ['key' => 'name', 'size' => 6, 'sizeXS' => 12, 'hideOnXS' => false],
        ['key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => true]
    ]);
});

it('allows to configure a column to fill left space', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer
                ->addField(EntityListField::make('name')->setWidthOnSmallScreens(4))
                ->addField(EntityListField::make('age'));
        }
    };

    expect($list->listLayout())->toEqual([
        ['key' => 'name', 'size' => 'fill', 'sizeXS' => 4, 'hideOnXS' => false],
        ['key' => 'age', 'size' => 'fill', 'sizeXS' => 'fill', 'hideOnXS' => false]
    ]);
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

        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer
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

        public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
        {
            $fieldsContainer
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

it('allows to configure a global message field without data', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configurePageAlert('template', null, 'test-key');
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['globalMessage'])->toEqual([
        'fieldKey' => 'test-key',
        'alertLevel' => null,
    ]);

    expect($list->listMetaFields()['test-key'])->toEqual(
        SharpShowHtmlField::make('test-key')->setInlineTemplate('template')->toArray()
    );
});

it('allows to configure a global message field with template data', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configurePageAlert('Hello {{name}}', null, 'test-key');
        }

        public function getGlobalMessageData(): ?array
        {
            return [
                'name' => 'Bob',
            ];
        }
    };

    $list->buildListConfig();

    expect($list->data()['test-key'])->toEqual(['name' => 'Bob']);
});

it('allows to configure a global message field with alert level', function() {
    $list = new class extends FakeSharpEntityList
    {
        public function buildListConfig(): void
        {
            $this->configurePageAlert('alert', static::$pageAlertLevelDanger);
        }
    };

    $list->buildListConfig();

    expect($list->listConfig()['globalMessage']['alertLevel'])->toEqual('danger');
});

it('we_can_configure_the_deletion_action_to_disallow_it', function() {
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

    expect($list->listConfig()['deleteHidden'])->toBeFalse();
    expect($list->listConfig()['deleteConfirmationText'])->toEqual('ok?');
});
