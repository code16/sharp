<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\GeolocationFormatter;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Tests\SharpTestCase;

class GeolocationFormatterTest extends SharpTestCase
{
    /** @test */
    public function we_can_format_value_to_front()
    {
        $formatter = new GeolocationFormatter();
        $field = SharpFormGeolocationField::make('geo');

        $this->assertEquals(['lat'=>12.3, 'lng'=>24.5], $formatter->toFront($field, '12.3,24.5'));
        $this->assertEquals(['lat'=>12.3, 'lng'=>24.5], $formatter->toFront($field, '12.3 , 24.5'));
        $this->assertNull($formatter->toFront($field, '12'));
        $this->assertNull($formatter->toFront($field, ''));
    }

    /** @test */
    public function we_can_format_value_from_front()
    {
        $formatter = new GeolocationFormatter();
        $attribute = 'attribute';
        $field = SharpFormGeolocationField::make('geo');

        $this->assertEquals('12.2,24.3', $formatter->fromFront($field, $attribute, ['lat'=>12.2, 'lng'=>24.3]));
        $this->assertNull($formatter->fromFront($field, $attribute, ''));
    }
}
