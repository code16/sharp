<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;

it('sets only default values', function() {
    $formField = SharpFormGeolocationField::make('geo');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'geo', 'type' => 'geolocation',
            'displayUnit' => 'DMS', 'geocoding' => false,
            'mapsProvider' => ['name' => 'gmaps', 'options' => []],
            'geocodingProvider' => ['name' => 'gmaps', 'options' => []],
            'zoomLevel' => 10,
        ]);
});

it('allows to define displayUnit', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setDisplayUnitDegreesMinutesSeconds();

    expect($formField->toArray())
        ->toHaveKey('displayUnit', 'DMS');

    $formField = SharpFormGeolocationField::make('geo')
        ->setDisplayUnitDecimalDegrees();

    expect($formField->toArray())
        ->toHaveKey('displayUnit', 'DD');
});

it('allows to turn on geocoding', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setGeocoding();

    expect($formField->toArray())
        ->toHaveKey('geocoding', true);
});

it('allows to define a global apiKey', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setApiKey('my-key');

    expect($formField->toArray())
        ->toHaveKey('mapsProvider', [
            'name' => 'gmaps', 'options' => ['apiKey' => 'my-key'],
        ])
        ->toHaveKey('geocodingProvider', [
            'name' => 'gmaps', 'options' => ['apiKey' => 'my-key'],
        ]);
});

it('allows to define maps or geocoding apiKey', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setMapsApiKey('my-key');

    expect($formField->toArray())
        ->toHaveKey('mapsProvider', [
            'name' => 'gmaps', 'options' => ['apiKey' => 'my-key'],
        ])
        ->toHaveKey('geocodingProvider', [
            'name' => 'gmaps', 'options' => [],
        ]);

    $formField = SharpFormGeolocationField::make('geo')
        ->setGeocodingApiKey('my-key');

    expect($formField->toArray())
        ->toHaveKey('mapsProvider', [
            'name' => 'gmaps', 'options' => [],
        ])
        ->toHaveKey('geocodingProvider', [
            'name' => 'gmaps', 'options' => ['apiKey' => 'my-key'],
        ]);
});

it('allows to define zoomLevel', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setZoomLevel(15);

    expect($formField->toArray())
        ->toHaveKey('zoomLevel', 15);
});

it('allows to define initialPosition', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setInitialPosition(12.4, -3.461894989013672);

    expect($formField->toArray())
        ->toHaveKey('initialPosition', [
            'lat' => 12.4, 'lng' => -3.461894989013672,
        ]);
});

it('allows to clear initialPosition', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setInitialPosition(12.4, 24.5);

    $formField->clearInitialPosition();

    expect($formField->toArray())
        ->not->toHaveKey('initialPosition');
});

it('allows to define boundaries', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setBoundaries(1, 2, 3, 4);

    expect($formField->toArray())
        ->toHaveKey('boundaries', [
            'ne' => ['lat' => 1, 'lng' => 2],
            'sw' => ['lat' => 3, 'lng' => 4],
        ]);
});

it('allows to define providers', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setMapsProvider('osm')
        ->setGeocodingProvider('osm');

    expect($formField->toArray())
        ->toHaveKey('mapsProvider', ['name' => 'osm', 'options' => []])
        ->toHaveKey('geocodingProvider', ['name' => 'osm', 'options' => []]);
});

it('allows to  set options for providers', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setMapsProvider('osm', [
            'tilesUrl' => 'test',
        ]);

    expect($formField->toArray())
        ->toHaveKey('mapsProvider', [
            'name' => 'osm',
            'options' => [
                'tilesUrl' => 'test',
            ],
        ]);

    $formField->setMapsApiKey('my-key');

    expect($formField->toArray())
        ->toHaveKey('mapsProvider', [
            'name' => 'osm',
            'options' => [
                'tilesUrl' => 'test',
                'apiKey' => 'my-key',
            ],
        ]);
});

it('disallows to define an unknown maps provider', function() {
    $this->expectException(SharpFormFieldValidationException::class);

    SharpFormGeolocationField::make('geo')
        ->setMapsProvider('apple')
        ->toArray();
});

it('disallows to define an unknown geocoding provider', function() {
    $this->expectException(SharpFormFieldValidationException::class);

    SharpFormGeolocationField::make('geo')
        ->setGeocodingProvider('apple')
        ->toArray();
});

it('allows to clear boundaries', function() {
    $formField = SharpFormGeolocationField::make('geo')
        ->setBoundaries(1, 2, 3, 4);

    $formField->clearBoundaries();

    expect($formField->toArray())
        ->not->toHaveKey('boundaries');
});
