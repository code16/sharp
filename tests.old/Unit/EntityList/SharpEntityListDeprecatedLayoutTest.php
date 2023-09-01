<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class SharpEntityListDeprecatedLayoutTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_layout_in_the_deprecated_way()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('name'))
                    ->addField(EntityListField::make('age'));
            }

            public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
            {
                $fieldsLayout->addColumn('name', 6)
                    ->addColumn('age', 6);
            }
        };

        $this->assertEquals(
            [
                [
                    'key' => 'name', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => false,
                ], [
                    'key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => false,
                ],
            ],
            $list->listLayout(),
        );
    }

    /** @test */
    public function we_can_define_a_layout_for_small_screens_in_the_deprecated_way()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('name'))
                    ->addField(EntityListField::make('age'));
            }

            public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
            {
                $fieldsLayout->addColumn('name', 6)
                    ->addColumn('age', 6);
            }

            public function buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout): void
            {
                $fieldsLayout->addColumn('name', 12);
            }
        };

        $this->assertEquals(
            [
                [
                    'key' => 'name', 'size' => 6, 'sizeXS' => 12, 'hideOnXS' => false,
                ], [
                    'key' => 'age', 'size' => 6, 'sizeXS' => 6, 'hideOnXS' => true,
                ],
            ],
            $list->listLayout(),
        );
    }

    /** @test */
    public function we_can_configure_a_column_to_fill_left_space_in_the_deprecated_way()
    {
        $list = new class extends SharpEntityDefaultTestList
        {
            public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
            {
                $fieldsContainer
                    ->addField(EntityListField::make('name'))
                    ->addField(EntityListField::make('age'));
            }

            public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
            {
                $fieldsLayout->addColumn('name', 4)
                    ->addColumn('age');
            }
        };

        $this->assertEquals(
            [
                [
                    'key' => 'name', 'size' => 4, 'sizeXS' => 4, 'hideOnXS' => false,
                ], [
                    'key' => 'age', 'size' => 'fill', 'sizeXS' => 'fill', 'hideOnXS' => false,
                ],
            ],
            $list->listLayout(),
        );
    }
}
