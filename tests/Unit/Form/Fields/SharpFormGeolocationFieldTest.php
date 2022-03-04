<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormGeolocationFieldTest extends SharpTestCase
{
    /** @test */
    public function only_default_values_are_set()
    {
        $formField = SharpFormGeolocationField::make('geo');

        $this->assertEquals(
            [
                'key'               => 'geo', 'type' => 'geolocation',
                'displayUnit'       => 'DMS', 'geocoding' => false,
                'mapsProvider'      => ['name' => 'gmaps', 'options' => []],
                'geocodingProvider' => ['name' => 'gmaps', 'options' => []],
                'zoomLevel'         => 10,
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_displayUnit()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setDisplayUnitDegreesMinutesSeconds();

        $this->assertArraySubset(
            ['displayUnit' => 'DMS'],
            $formField->toArray()
        );

        $formField = SharpFormGeolocationField::make('geo')
            ->setDisplayUnitDecimalDegrees();

        $this->assertArraySubset(
            ['displayUnit' => 'DD'],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_turn_on_geocoding()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setGeocoding();

        $this->assertArraySubset(
            ['geocoding' => true],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_a_global_apiKey()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setApiKey('my-key');

        $this->assertArraySubset(
            [
                'mapsProvider'      => ['name' => 'gmaps', 'options' => ['apiKey' => 'my-key']],
                'geocodingProvider' => ['name' => 'gmaps', 'options' => ['apiKey' => 'my-key']],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_maps_or_geocoding_apiKey()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setMapsApiKey('my-key');

        $this->assertArraySubset(
            [
                'mapsProvider'      => ['name' => 'gmaps', 'options' => ['apiKey' => 'my-key']],
                'geocodingProvider' => ['name' => 'gmaps', 'options' => []],
            ],
            $formField->toArray()
        );

        $formField = SharpFormGeolocationField::make('geo')
            ->setGeocodingApiKey('my-key');

        $this->assertArraySubset(
            [
                'mapsProvider'      => ['name' => 'gmaps', 'options' => []],
                'geocodingProvider' => ['name' => 'gmaps', 'options' => ['apiKey' => 'my-key']],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_zoomLevel()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setZoomLevel(15);

        $this->assertArraySubset(
            ['zoomLevel' => 15],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_initialPosition()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setInitialPosition(12.4, -3.461894989013672);

        $this->assertArraySubset(
            [
                'initialPosition' => [
                    'lat' => 12.4, 'lng' => -3.461894989013672,
                ],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_clear_initialPosition()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setInitialPosition(12.4, 24.5);

        $formField->clearInitialPosition();

        $this->assertArrayNotHasKey(
            'initialPosition',
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_boundaries()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setBoundaries(1, 2, 3, 4);

        $this->assertArraySubset(
            [
                'boundaries' => [
                    'ne' => [
                        'lat' => 1, 'lng' => 2,
                    ],
                    'sw' => [
                        'lat' => 3, 'lng' => 4,
                    ],
                ],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_providers()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setMapsProvider('osm')
            ->setGeocodingProvider('osm');

        $this->assertArraySubset(
            [
                'mapsProvider'      => ['name' => 'osm', 'options' => []],
                'geocodingProvider' => ['name' => 'osm', 'options' => []],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_set_options_for_providers()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setMapsProvider('osm', [
                'tilesUrl' => 'test',
            ]);

        $this->assertArraySubset(
            [
                'mapsProvider' => [
                    'name' => 'osm', 'options' => [
                        'tilesUrl' => 'test',
                    ],
                ],
            ],
            $formField->toArray()
        );

        $formField->setMapsApiKey('my-key');

        $this->assertArraySubset(
            [
                'mapsProvider' => [
                    'name' => 'osm', 'options' => [
                        'tilesUrl' => 'test',
                        'apiKey'   => 'my-key',
                    ],
                ],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_cant_define_an_unknown_maps_provider()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormGeolocationField::make('geo')
            ->setMapsProvider('apple')
            ->toArray();
    }

    /** @test */
    public function we_cant_define_an_unknown_geocoding_provider()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormGeolocationField::make('geo')
            ->setGeocodingProvider('apple')
            ->toArray();
    }

    /** @test */
    public function we_can_clear_boundaries()
    {
        $formField = SharpFormGeolocationField::make('geo')
            ->setBoundaries(1, 2, 3, 4);

        $formField->clearBoundaries();

        $this->assertArrayNotHasKey(
            'boundaries',
            $formField->toArray()
        );
    }
}
