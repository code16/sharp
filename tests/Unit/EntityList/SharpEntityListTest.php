<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

class SharpEntityListTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_containers()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer->addField(
                    EntityListField::make('name')
                        ->setLabel('Name'),
                );
            }
        };

        $this->assertEquals(
            [
                'name' => [
                    'key' => 'name',
                    'label' => 'Name',
                    'sortable' => false,
                    'html' => true,
                ],
            ],
            $list->fields(),
        );
    }

    /** @test */
    public function we_can_get_layout()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('name')->setWidth(6))
                    ->addField(EntityListField::make('age')->setWidth(6));
            }
        };

        $this->assertEquals(
            [
                ['key' => 'name', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => false],
                ['key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => false],
            ],
            $list->listLayout(),
        );
    }

    /** @test */
    public function we_can_define_a_layout_for_small_screens()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('name')->setWidth(6)->setWidthOnSmallScreens(12))
                    ->addField(EntityListField::make('age')->setWidth(6)->hideOnSmallScreens());
            }
        };

        $this->assertEquals(
            [
                ['key' => 'name', 'size' => 6, 'sizeXS' => 12, 'hideOnXS' => false],
                ['key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => true],
            ],
            $list->listLayout(),
        );
    }

    /** @test */
    public function we_can_configure_a_column_to_fill_left_space()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('name')->setWidthOnSmallScreens(4))
                    ->addField(EntityListField::make('age'));
            }
        };

        $this->assertEquals(
            [
                ['key' => 'name', 'size' => 'fill', 'sizeXS' => 4, 'hideOnXS' => false],
                ['key' => 'age', 'size' => 'fill', 'sizeXS' => 'fill', 'hideOnXS' => false],
            ],
            $list->listLayout(),
        );
    }

    /** @test */
    public function we_can_get_list_data()
    {
        $list = new class extends SharpEntityDefaultTestList
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
                    ->addField(
                        EntityListField::make('name'),
                    )
                    ->addField(
                        EntityListField::make('age'),
                    );
            }
        };

        $this->assertEquals(
            [
                'items' => [
                    ['name' => 'John Wayne', 'age' => 22],
                    ['name' => 'Mary Wayne', 'age' => 26],
                ],
            ],
            $list->data()['list'],
        );
    }

    /** @test */
    public function we_can_get_paginated_list_data()
    {
        $list = new class extends SharpEntityDefaultTestList
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
                    ->addField(
                        EntityListField::make('name'),
                    )
                    ->addField(
                        EntityListField::make('age'),
                    );
            }
        };

        $this->assertEquals(
            [
                'items' => [
                    ['name' => 'John Wayne', 'age' => 22],
                    ['name' => 'Mary Wayne', 'age' => 26],
                ],
                'page' => 1,
                'pageSize' => 2,
                'totalCount' => 10,
            ],
            $list->data()['list'],
        );
    }

    /** @test */
    public function we_can_get_list_config()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->configureSearchable()
                    ->configurePaginated();
            }
        };

        $list->buildListConfig();

        $this->assertEquals(
            [
                'searchable' => true,
                'paginated' => true,
                'reorderable' => false,
                'hasShowPage' => false,
                'instanceIdAttribute' => 'id',
                'multiformAttribute' => null,
                'defaultSort' => null,
                'defaultSortDir' => null,
                'delete' => [
                    'allowed' => true,
                    'confirmationText' => trans('sharp::show.delete_confirmation_text')
                ],
            ],
            $list->listConfig(),
        );
    }

    /** @test */
    public function we_can_configure_a_global_message_field_without_data()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->configurePageAlert('template', null, 'test-key');
            }
        };

        $list->buildListConfig();

        $this->assertEquals('test-key', $list->listConfig()['globalMessage']['fieldKey']);
        $this->assertEquals(
            SharpShowHtmlField::make('test-key')->setInlineTemplate('template')->toArray(),
            $list->listMetaFields()['test-key'],
        );
    }

    /** @test */
    public function we_can_configure_a_global_message_field_with_template_data()
    {
        $list = new class extends SharpEntityDefaultTestList
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

        $this->assertEquals(
            ['name' => 'Bob'],
            $list->data()['test-key'],
        );
    }

    /** @test */
    public function we_can_configure_a_global_message_field_with_alert_level()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->configurePageAlert('alert', static::$pageAlertLevelDanger);
            }
        };

        $list->buildListConfig();

        $this->assertEquals(
            'danger',
            $list->listConfig()['globalMessage']['alertLevel'],
        );
    }

    /** @test */
    public function we_can_configure_the_deletion_action_to_disallow_it()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->configureDelete(false);
            }
        };

        $list->buildListConfig();

        $this->assertFalse($list->listConfig()['delete']['allowed']);
    }

    /** @test */
    public function we_can_configure_the_deletion_action_confirmation_text()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListConfig(): void
            {
                $this->configureDelete(message: 'ok?');
            }
        };

        $list->buildListConfig();

        $this->assertTrue($list->listConfig()['delete']['allowed']);
        $this->assertEquals('ok?', $list->listConfig()['delete']['confirmationText']);
    }
}
