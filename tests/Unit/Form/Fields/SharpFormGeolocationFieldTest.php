<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormGeolocationFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormGeolocationField::make("geo");

        $this->assertEquals([
                "key" => "geo", "type" => "geolocation",
                "displayUnit" => "DMS", "geocoding" => false
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_displayUnit()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setDisplayUnitDegreesMinutesSeconds();

        $this->assertArraySubset(
            ["displayUnit" => "DMS"],
            $formField->toArray()
        );

        $formField = SharpFormGeolocationField::make("geo")
            ->setDisplayUnitDecimalDegrees();

        $this->assertArraySubset(
            ["displayUnit" => "DD"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_geocoding_and_apiKey()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setGeocoding()
            ->setApiKey("my-key");

        $this->assertArraySubset(
            ["geocoding" => true, "apiKey" => "my-key"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_zoomLevel()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setZoomLevel(10);

        $this->assertArraySubset(
            ["zoomLevel" => 10],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_boundaries()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setBoundaries(1, 2, 3, 4);

        $this->assertArraySubset(
            [
                "boundaries" => [
                    "ne" => [
                        "lat" => 1, "lng" => 2
                    ],
                    "sw" => [
                        "lat" => 3, "lng" => 4
                    ]
                ]
            ],
            $formField->toArray()
        );
    }
}