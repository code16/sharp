<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Str;

class SharpFormAutocompleteFieldTest extends SharpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        @unlink(resource_path("views/LIT.vue"));
        @unlink(resource_path("views/RIT.vue"));
        file_put_contents(resource_path("views/LIT.vue"), "LIT-content");
        file_put_contents(resource_path("views/RIT.vue"), "RIT-content");
    }

    /** @test */
    function only_default_values_are_set()
    {
        $localValues = [
            1  => "bob"
        ];

        $defaultFormField = $this->getDefaultLocalAutocomplete($localValues);

        $this->assertEquals(
            [
                "key" => "field", "type" => "autocomplete",
                "mode" => "local", "searchKeys" => ["value"],
                "remoteMethod" => "GET", "itemIdAttribute" => "id",
                "listItemTemplate" => "LIT-content",
                "resultItemTemplate" => "RIT-content",
                "searchMinChars" => 1, "localValues" => [
                    ["id" => 1, "label" => "bob"]
                ],
                "remoteSearchAttribute" => "query",
                "templateData" => [],
                "dataWrapper" => "",
                "debounceDelay" => 300,
            ], 
            $defaultFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_remote_attributes()
    {
        $formField = SharpFormAutocompleteField::make("field", "remote")
            ->setListItemTemplatePath("LIT.vue")
            ->setResultItemTemplatePath("RIT.vue")
            ->setRemoteMethodPOST()
            ->setRemoteEndpoint("endpoint")
            ->setRemoteSearchAttribute("attribute");

        $this->assertArraySubset(
            [
                "remoteMethod" => "POST", "remoteEndpoint" => "endpoint",
                "remoteSearchAttribute" => "attribute"
            ], 
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localValues_as_a_id_label_array()
    {
        $formField = $this->getDefaultLocalAutocomplete([
            ["id" => 1, "label" => "Elem 1"],
            ["id" => 2, "label" => "Elem 2"],
        ]);

        $this->assertArraySubset(
            ["localValues" => [
                ["id" => 1, "label" => "Elem 1"],
                ["id" => 2, "label" => "Elem 2"],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localValues_as_an_object_array()
    {
        $formField = $this->getDefaultLocalAutocomplete([
            (object)["id" => 1, "label" => "Elem 1"],
            (object)["id" => 2, "label" => "Elem 2"],
        ]);

        $this->assertArraySubset(
            ["localValues" => [
                (object)["id" => 1, "label" => "Elem 1"],
                (object)["id" => 2, "label" => "Elem 2"],
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_searchMinChars()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setSearchMinChars(3);

        $this->assertArraySubset(
            [
                "searchMinChars" => 3
            ], 
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_debounceDelay()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setDebounceDelayInMilliseconds(500);

        $this->assertArraySubset(
            [
                "debounceDelay" => 500
            ],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_setDataWrapper()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setDataWrapper("test");

        $this->assertArraySubset(
            [
                "dataWrapper" => "test"
            ], 
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_inline_templates()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setListItemInlineTemplate('<strong>LIT</strong>')
            ->setResultItemInlineTemplate('<strong>RIT</strong>');

        $this->assertArraySubset(
            [
                "listItemTemplate" => "<strong>LIT</strong>",
                "resultItemTemplate" => "<strong>RIT</strong>"
            ], 
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_templateData()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setAdditionalTemplateData([
                "lang" => ["fr", "de"]
            ]);

        $this->assertArraySubset(
            [
                "templateData" => [
                    "lang" => ["fr", "de"]
                ]
            ],
            $formField->toArray()
        );
    }

    /** @test */
    function we_cant_define_a_remote_autocomplete_without_remoteEndpoint()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormAutocompleteField::make("field", "remote")
            ->setListItemTemplatePath("LIT.vue")
            ->setResultItemTemplatePath("RIT.vue")
            ->toArray();
    }

    /** @test */
    function we_can_define_linked_localValues_with_dynamic_attributes()
    {
        $formField = $this->getDefaultLocalAutocomplete([
            "A" => [
                "A1" => "test A1",
                "A2" => "test A2"
            ],
            "B" => [
                "B1" => "test B1",
                "B2" => "test B2"
            ]
        ])->setLocalValuesLinkedTo("master");

        $this->assertArraySubset(
            ["localValues" => [
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
    function we_can_define_linked_localValues_with_dynamic_attributes_and_localization()
    {
        $formField = $this->getDefaultLocalAutocomplete([
            "A" => [
                "A1" => ["fr" => "test A1 fr", "en" => "test A1 en"],
                "A2" => ["fr" => "test A2 fr", "en" => "test A2 en"]
            ],
            "B" => [
                "B1" => ["fr" => "test B1 fr", "en" => "test B1 en"],
                "B2" => ["fr" => "test B2 fr", "en" => "test B2 en"]
            ]
        ])->setLocalValuesLinkedTo("master")->setLocalized();

        $this->assertArraySubset(
            ["localValues" => [
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
    function we_can_define_linked_localValues_with_dynamic_attributes_on_multiple_master_fields()
    {
        $formField = $this->getDefaultLocalAutocomplete([
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
        ])->setLocalValuesLinkedTo("master", "master2");

        $this->assertArraySubset(
            ["localValues" => [
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

    /** @test */
    function we_can_define_linked_remote_endpoint_with_dynamic_attributes()
    {
        $formField = $this->getDefaultDynamicRemoteAutocomplete(
            "autocomplete/{{master}}/endpoint"
        );

        $this->assertArraySubset([
                "remoteEndpoint" => "autocomplete/{{master}}/endpoint",
                "dynamicAttributes" => [
                    [
                        "name" => "remoteEndpoint",
                        "type" => "template",
                    ]
                ]
            ],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_linked_remote_endpoint_with_default_value_with_dynamic_attributes()
    {
        $master = Str::random(4);

        $formField = $this->getDefaultDynamicRemoteAutocomplete(
            "autocomplete/{{master}}/endpoint", [
                "master" => $master
            ]
        );

        $this->assertArraySubset([
                "remoteEndpoint" => "autocomplete/{{master}}/endpoint",
                "dynamicAttributes" => [
                    [
                        "name" => "remoteEndpoint",
                        "type" => "template",
                        "default" => "autocomplete/$master/endpoint"
                    ]
                ]
            ],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_linked_remote_endpoint_with_multiple_default_value_with_dynamic_attributes()
    {
        $master = Str::random(4);
        $secondary = Str::random(4);

        $formField = $this->getDefaultDynamicRemoteAutocomplete(
            "autocomplete/{{master}}/{{secondary}}/endpoint", [
                "master" => $master,
                "secondary" => $secondary,
            ]
        );

        $this->assertArraySubset([
                "remoteEndpoint" => "autocomplete/{{master}}/{{secondary}}/endpoint",
                "dynamicAttributes" => [
                    [
                        "name" => "remoteEndpoint",
                        "type" => "template",
                        "default" => "autocomplete/$master/$secondary/endpoint"
                    ]
                ]
            ],
            $formField->toArray()
        );
    }

    /**
     * @param array|null $localValues
     * @return SharpFormAutocompleteField
     */
    private function getDefaultLocalAutocomplete($localValues = null)
    {
        return SharpFormAutocompleteField::make("field", "local")
            ->setListItemTemplatePath("LIT.vue")
            ->setResultItemTemplatePath("RIT.vue")
            ->setLocalValues($localValues ?: [
                1  => "bob"
            ]);
    }

    /**
     * @param string $remoteEndpoint
     * @param array $defaultValues
     * @return SharpFormAutocompleteField
     */
    private function getDefaultDynamicRemoteAutocomplete($remoteEndpoint, array $defaultValues = [])
    {
        return SharpFormAutocompleteField::make("field", "remote")
            ->setListItemTemplatePath("LIT.vue")
            ->setResultItemTemplatePath("RIT.vue")
            ->setDynamicRemoteEndpoint($remoteEndpoint, $defaultValues);
    }
}