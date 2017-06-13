<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Exceptions\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormAutocompleteFieldTest extends SharpTestCase
{
    protected function setUp()
    {
        parent::setUp();

        unlink(resource_path("views/LIT.vue"));
        unlink(resource_path("views/RIT.vue"));
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

        $this->assertArraySubset([
                "remoteMethod" => "POST", "remoteEndpoint" => "endpoint",
                "remoteSearchAttribute" => "attribute"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_itemIdAttribute()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setItemIdAttribute("attribute");

        $this->assertArraySubset([
                "itemIdAttribute" => "attribute"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_searchMinChars()
    {
        $formField = $this->getDefaultLocalAutocomplete()
            ->setSearchMinChars(3);

        $this->assertArraySubset([
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

        $this->assertArraySubset([
            "listItemTemplate" => "<strong>LIT</strong>",
            "resultItemTemplate" => "<strong>RIT</strong>"
        ], $formField->toArray()
        );
    }

    /** @test */
    function we_cant_define_a_local_autocomplete_without_local_values()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormAutocompleteField::make("field", "local")
            ->setListItemTemplatePath("LIT.vue")
            ->setResultItemTemplatePath("RIT.vue")
            ->toArray();
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