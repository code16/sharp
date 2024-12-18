<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormListFieldTest extends SharpTestCase
{
    /** @test */
    public function only_default_values_are_set()
    {
        $formField = $this->getDefaultList();

        $this->assertEquals(
            [
                'key' => 'field', 'type' => 'list',
                'addable' => false, 'removable' => false, 'sortable' => false,
                'addText' => 'Add an item', 'itemIdAttribute' => 'id',
                'bulkUploadLimit' => 10,
                'itemFields' => [
                    'text' => [
                        'key' => 'text',
                        'type' => 'text',
                        'inputType' => 'text',
                    ],
                ],
            ],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_addable_removable_and_sortable()
    {
        $formField = $this->getDefaultList()
            ->setAddable()
            ->setSortable()
            ->setRemovable();

        $this->assertArraySubset(
            ['addable' => true, 'removable' => true, 'sortable' => true],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_add_text()
    {
        $formField = $this->getDefaultList()
            ->setAddText('Add');

        $this->assertArraySubset(
            ['addText' => 'Add'],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_item_id_attribute()
    {
        $formField = $this->getDefaultList()
            ->setItemIdAttribute('key');

        $this->assertArraySubset(
            ['itemIdAttribute' => 'key'],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_order_attribute()
    {
        $formField = $this->getDefaultList()
            ->setOrderAttribute('ordered');

        $this->assertEquals('ordered', $formField->orderAttribute());
    }

    /** @test */
    public function we_can_define_max_item_count()
    {
        $formField = $this->getDefaultList()
            ->setMaxItemCount(10);

        $this->assertArraySubset(
            ['maxItemCount' => 10],
            $formField->toArray(),
        );

        $formField->setMaxItemCountUnlimited();

        $this->assertArrayNotHasKey(
            'maxItemCount',
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_allow_bulk_upload_for_field()
    {
        $formField = $this->getDefaultList()
            ->allowBulkUploadForField('itemFieldKey');

        $this->assertArraySubset(
            ['bulkUploadField' => 'itemFieldKey'],
            $formField->toArray(),
        );

        $formField->doNotAllowBulkUpload();

        $this->assertArrayNotHasKey(
            'bulkUploadField',
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_set_bulk_upload_file_count_limit_at_once()
    {
        $formField = $this->getDefaultList()
            ->setBulkUploadFileCountLimitAtOnce(8);

        $this->assertArraySubset(
            ['bulkUploadLimit' => 8],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_collapsed_item_inline_template()
    {
        $formField = $this->getDefaultList()
            ->setCollapsedItemInlineTemplate('template');

        $this->assertArraySubset(
            ['collapsedItemTemplate' => 'template'],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_add_an_item_field()
    {
        $formField = $this->getDefaultList()
            ->addItemField(
                SharpFormTextField::make('name'),
            );

        $this->assertArraySubset([
            'itemFields' => [
                'text' => ['key' => 'text', 'type' => 'text', 'inputType' => 'text'],
                'name' => ['key' => 'name', 'type' => 'text', 'inputType' => 'text'],
            ], ], $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_find_an_item_field_by_its_key()
    {
        $formField = $this->getDefaultList()
            ->addItemField(
                SharpFormTextField::make('name'),
            );

        $this->assertEquals('name', $formField->findItemFormFieldByKey('name')->toArray()['key']);
    }

    /**
     * @return SharpFormListField
     */
    protected function getDefaultList()
    {
        return SharpFormListField::make('field')
            ->addItemField(
                SharpFormTextField::make('text'),
            );
    }
}
