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
                "displayUnit" => "DMS", "geocoding" => false,
                "zoomLevel" => 10
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_displayUnit()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setDisplayUnitDegreesMinutesSeconds();

        $this->assertArrayContainsSubset(
            ["displayUnit" => "DMS"],
            $formField->toArray()
        );

        $formField = SharpFormGeolocationField::make("geo")
            ->setDisplayUnitDecimalDegrees();

        $this->assertArrayContainsSubset(
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

        $this->assertArrayContainsSubset(
            ["geocoding" => true, "apiKey" => "my-key"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_zoomLevel()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setZoomLevel(15);

        $this->assertArrayContainsSubset(
            ["zoomLevel" => 15],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_initialPosition()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setInitialPosition(12.4, -3.461894989013672);

        $this->assertArrayContainsSubset(
            [
                "initialPosition" => [
                    "lat" => 12.4, "lng" => -3.461894989013672
                ]
            ],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_clear_initialPosition()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setInitialPosition(12.4, 24.5);

        $formField->clearInitialPosition();

        $this->assertArrayNotHasKey(
            "initialPosition",
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_boundaries()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setBoundaries(1, 2, 3, 4);

        $this->assertArrayContainsSubset(
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

    /** @test */
    function we_can_clear_boundaries()
    {
        $formField = SharpFormGeolocationField::make("geo")
            ->setBoundaries(1, 2, 3, 4);

        $formField->clearBoundaries();

        $this->assertArrayNotHasKey(
            "boundaries",
            $formField->toArray()
        );
    }
}