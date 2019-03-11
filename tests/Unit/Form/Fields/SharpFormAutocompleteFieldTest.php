<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Tests\SharpTestCase;

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

        $this->assertEquals([
                "key" => "field", "type" => "autocomplete",
                "mode" => "local", "searchKeys" => ["value"],
                "remoteMethod" => "GET", "itemIdAttribute" => "id",
                "listItemTemplate" => "LIT-content",
                "resultItemTemplate" => "RIT-content",
                "searchMinChars" => 1, "localValues" => [
                    ["id" => 1, "label" => "bob"]
                ],
                "remoteSearchAttribute" => "query"
            ], $defaultFormField->toArray()
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

        $this->assertArrayContainsSubset([
                "remoteMethod" => "POST", "remoteEndpoint" => "endpoint",
                "remoteSearchAttribute" => "attribute"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_localValues_as_a_id_label_array()
    {
        $formField = $this->getDefaultLocalAutocomplete([
            ["id" => 1, "label" => "Elem 1"],
            ["id" => 2, "label" => "Elem 2"],
        ]);

        $this->assertArrayContainsSubset(
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

        $this->assertArrayContainsSubset(
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

        $this->assertArrayContainsSubset([
                "searchMinChars" => 3
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_inline_templates()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setListItemInlineTemplate('<strong>LIT</strong>')
            ->setResultItemInlineTemplate('<strong>RIT</strong>');

        $this->assertArrayContainsSubset([
            "listItemTemplate" => "<strong>LIT</strong>",
            "resultItemTemplate" => "<strong>RIT</strong>"
        ], $formField->toArray()
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

}