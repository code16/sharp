<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormSelectFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $options = [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ];

        $formField = $this->getDefaultSelect($options);

        $this->assertEquals([
                "key" => "field", "type" => "select",
                "options" => [
                    ["id" => "1", "label" => "Elem 1"],
                    ["id" => "2", "label" => "Elem 2"],
                ], "multiple" => false,
                "clearable" => false, "inline" => false, "display" => "list",
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_multiple()
    {
        $formField = $this->getDefaultSelect()
            ->setMultiple(true);

        $this->assertArrayContainsSubset(
            ["multiple" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_inline()
    {
        $formField = $this->getDefaultSelect()
            ->setInline();

        $this->assertArrayContainsSubset(
            ["inline" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_maxSelected()
    {
        $formField = $this->getDefaultSelect()
            ->setMaxSelected(12);

        $this->assertArrayContainsSubset(
            ["maxSelected" => 12],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_clearable()
    {
        $formField = $this->getDefaultSelect()
            ->setClearable(true);

        $this->assertArrayContainsSubset(
            ["clearable" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_display_as_list()
    {
        $formField = $this->getDefaultSelect()
            ->setDisplayAsList();

        $this->assertArrayContainsSubset(
            ["display" => "list"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_display_as_dropdown()
    {
        $formField = $this->getDefaultSelect()
            ->setDisplayAsDropdown();

        $this->assertArrayContainsSubset(
            ["display" => "dropdown"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_options_as_a_id_label_array()
    {
        $formField = $this->getDefaultSelect([
            ["id" => 1, "label" => "Elem 1"],
            ["id" => 2, "label" => "Elem 2"],
        ]);

        $this->assertArrayContainsSubset(
            ["options" => [
                ["id" => 1, "label" => "Elem 1"],
                ["id" => 2, "label" => "Elem 2"],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localized_options()
    {
        $formField = $this->getDefaultSelect([
            "1" => ["en" => "Option one", "fr" => "Option un"],
            "2" => ["en" => "Option two", "fr" => "Option deux"],
        ])->setLocalized();

        $this->assertArrayContainsSubset(
            ["options" => [
                ["id" => 1, "label" => ["en" => "Option one", "fr" => "Option un"]],
                ["id" => 2, "label" => ["en" => "Option two", "fr" => "Option deux"]],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localized_options_with_id_label_array()
    {
        $formField = $this->getDefaultSelect([
            ["id" => "1", "label" => ["en" => "Option one", "fr" => "Option un"]],
            ["id" => "2", "label" => ["en" => "Option two", "fr" => "Option deux"]],
        ])->setLocalized();

        $this->assertArrayContainsSubset(
            ["options" => [
                ["id" => 1, "label" => ["en" => "Option one", "fr" => "Option un"]],
                ["id" => 2, "label" => ["en" => "Option two", "fr" => "Option deux"]],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_linked_options_with_dynamic_attributes()
    {
        $formField = $this->getDefaultSelect([
            "A" => [
                "A1" => "test A1",
                "A2" => "test A2"
            ],
            "B" => [
                "B1" => "test B1",
                "B2" => "test B2"
            ]
        ])->setOptionsLinkedTo("master");

        $this->assertArrayContainsSubset(
            ["options" => [
                "A" => [
                    ["id" => "A1", "label" => "test A1"],
                    ["id" => "A2", "label" => "test A2"],
                ],
                "B" => [
                    ["id" => "B1", "label" => "test B1"],
                    ["id" => "B2", "label" => "test B2"],
                ]
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_linked_options_with_dynamic_attributes_and_localization()
    {
        $formField = $this->getDefaultSelect([
            "A" => [
                "A1" => ["fr" => "test A1 fr", "en" => "test A1 en"],
                "A2" => ["fr" => "test A2 fr", "en" => "test A2 en"]
            ],
            "B" => [
                "B1" => ["fr" => "test B1 fr", "en" => "test B1 en"],
                "B2" => ["fr" => "test B2 fr", "en" => "test B2 en"]
            ]
        ])->setOptionsLinkedTo("master")->setLocalized();

        $this->assertArrayContainsSubset(
            ["options" => [
                "A" => [
                    ["id" => "A1", "label" => ["fr" => "test A1 fr", "en" => "test A1 en"]],
                    ["id" => "A2", "label" => ["fr" => "test A2 fr", "en" => "test A2 en"]],
                ],
                "B" => [
                    ["id" => "B1", "label" => ["fr" => "test B1 fr", "en" => "test B1 en"]],
                    ["id" => "B2", "label" => ["fr" => "test B2 fr", "en" => "test B2 en"]],
                ]
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_linked_options_with_dynamic_attributes_on_multiple_master_fields()
    {
        $formField = $this->getDefaultSelect([
            "A" => [
                "A1" => [
                    "A11" => "test A11",
                    "A12" => "test A12"
                ],
                "A2" => [
                    "A21" => "test A21",
                    "A22" => "test A22"
                ],
            ],
            "B" => [
                "B1" => [
                    "B11" => "test B11",
                    "B12" => "test B12"
                ]
            ]
        ])->setOptionsLinkedTo("master", "master2");

        $this->assertArrayContainsSubset(
            ["options" => [
                "A" => [
                    "A1" => [
                        ["id" => "A11", "label" => "test A11"],
                        ["id" => "A12", "label" => "test A12"],
                    ],
                    "A2" => [
                        ["id" => "A21", "label" => "test A21"],
                        ["id" => "A22", "label" => "test A22"],
                    ]
                ],
                "B" => [
                    "B1" => [
                        ["id" => "B11", "label" => "test B11"],
                        ["id" => "B12", "label" => "test B12"],
                    ]
                ]
            ]],
            $formField->toArray()
        );
    }

    /**
     * @param array|null $options
     * @return SharpFormSelectField
     */
    private function getDefaultSelect($options = null)
    {
        return SharpFormSelectField::make("field", $options ?: [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ]);
    }
}