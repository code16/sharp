<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormListFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = $this->getDefaultList();

        $this->assertEquals([
                "key" => "field", "type" => "list",
                "addable" => false, "removable" => false, "sortable" => false,
                "addText" => "Add an item", "itemIdAttribute" => "id",
                "itemFields" => [
                    "text" => [
                        "key" => "text",
                        "type" => "text",
                        "inputType" => "text",
                        "maxLength" => 0
                    ]
                ]
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_addable_removable_and_sortable()
    {
        $formField = $this->getDefaultList()
            ->setAddable()
            ->setSortable()
            ->setRemovable();

        $this->assertArraySubset(
            ["addable" => true, "removable" => true, "sortable" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_addText()
    {
        $formField = $this->getDefaultList()
            ->setAddText("Add");

        $this->assertArraySubset(
            ["addText" => "Add"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_itemIdAttribute()
    {
        $formField = $this->getDefaultList()
            ->setItemIdAttribute("key");

        $this->assertArraySubset(
            ["itemIdAttribute" => "key"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_orderAttribute()
    {
        $formField = $this->getDefaultList()
            ->setOrderAttribute("ordered");

        $this->assertEquals("ordered", $formField->orderAttribute());
    }

    /** @test */
    function we_can_define_maxItemCount()
    {
        $formField = $this->getDefaultList()
            ->setMaxItemCount(10);

        $this->assertArraySubset(
            ["maxItemCount" => 10],
            $formField->toArray()
        );

        $formField->setMaxItemCountUnlimited();

        $this->assertArrayNotHasKey(
            "maxItemCount",
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_collapsedItemInlineTemplate()
    {
        $formField = $this->getDefaultList()
            ->setCollapsedItemInlineTemplate("template");

        $this->assertArraySubset(
            ["collapsedItemTemplate" => "template"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_add_an_itemField()
    {
        $formField = $this->getDefaultList()
            ->addItemField(
                SharpFormTextField::make("name")
            );

        $this->assertArraySubset([
            "itemFields" => [
                "text" => ["key" => "text", "type" => "text", "inputType" => "text"],
                "name" => ["key" => "name", "type" => "text", "inputType" => "text"],
            ]], $formField->toArray()
        );
    }

    /** @test */
    function we_can_find_an_itemField_by_its_key()
    {
        $formField = $this->getDefaultList()
            ->addItemField(
                SharpFormTextField::make("name")
            );

        $this->assertEquals("name", $formField->findItemFormFieldByKey("name")->toArray()["key"]);
    }

    /**
     * @return SharpFormListField
     */
    protected function getDefaultList()
    {
        return SharpFormListField::make("field")
            ->addItemField(
                SharpFormTextField::make("text")
            );
    }
}