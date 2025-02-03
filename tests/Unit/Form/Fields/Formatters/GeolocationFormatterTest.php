<?php

use Code16\Sharp\Form\Fields\Formatters\GeolocationFormatter;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;

it('formats value to front', function () {
    $formatter = new GeolocationFormatter();
    $field = SharpFormGeolocationField::make('geo');

    $this->assertEquals(['lat' => 12.3, 'lng' => 24.5], $formatter->toFront($field, '12.3,24.5'));
    $this->assertEquals(['lat' => 12.3, 'lng' => 24.5], $formatter->toFront($field, '12.3 , 24.5'));
    $this->assertNull($formatter->toFront($field, '12'));
    $this->assertNull($formatter->toFront($field, ''));
});

it('formats value from front', function () {
    $formatter = new GeolocationFormatter();
    $attribute = 'attribute';
    $field = SharpFormGeolocationField::make('geo');

    $this->assertEquals('12.2,24.3', $formatter->fromFront($field, $attribute, ['lat' => 12.2, 'lng' => 24.3]));
    $this->assertNull($formatter->fromFront($field, $attribute, ''));
});
