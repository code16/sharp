<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Carbon\Carbon;
use Code16\Sharp\Form\Exceptions\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormAutocompleteFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $localValues = [
            "id" => 1, "name" => "bob"
        ];

        $defaultFormField = $this->getDefaultLocalAutocomplete($localValues);

        $this->assertEquals([
                "key" => "field", "type" => "autocomplete",
                "mode" => "local", "searchKeys" => ["value"],
                "remoteMethod" => "GET", "itemIdAttribute" => "id",
                "listItemTemplate" => resource_path("views/LIT"),
                "resultItemTemplate" => resource_path("views/RIT"),
                "searchMinChars" => 1, "localValues" => $localValues,
                "remoteSearchAttribute" => "query"
            ], $defaultFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_remote_attributes()
    {
        $formField = SharpFormAutocompleteField::make("field", "remote")
            ->setListItemTemplatePath("LIT")
            ->setResultItemTemplatePath("RIT")
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
    function we_cant_define_a_local_autocomplete_without_local_values()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormAutocompleteField::make("field", "local")
            ->setListItemTemplatePath("LIT")
            ->setResultItemTemplatePath("RIT")
            ->toArray();
    }

    /** @test */
    function we_cant_define_a_remote_autocomplete_without_remoteEndpoint()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormAutocompleteField::make("field", "remote")
            ->setListItemTemplatePath("LIT")
            ->setResultItemTemplatePath("RIT")
            ->toArray();
    }

    /**
     * @param array|null $localValues
     * @return SharpFormAutocompleteField
     */
    private function getDefaultLocalAutocomplete($localValues = null)
    {
        return SharpFormAutocompleteField::make("field", "local")
            ->setListItemTemplatePath("LIT")
            ->setResultItemTemplatePath("RIT")
            ->setLocalValues($localValues ?: [
                "id" => 1, "name" => "bob"
            ]);
    }

}