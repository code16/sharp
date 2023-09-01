<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\CheckFormatter;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Tests\SharpTestCase;

class CheckFormatterTest extends SharpTestCase
{
    /** @test */
    public function we_can_format_value_to_front()
    {
        $formatter = new CheckFormatter;
        $field = SharpFormCheckField::make('check', 'text');

        $this->assertTrue($formatter->toFront($field, true));
        $this->assertTrue($formatter->toFront($field, 1));

        $this->assertFalse($formatter->toFront($field, false));
        $this->assertFalse($formatter->toFront($field, 0));
    }

    /** @test */
    public function we_can_format_value_from_front()
    {
        $formatter = new CheckFormatter;
        $field = SharpFormCheckField::make('check', 'text');
        $attribute = 'attribute';

        $this->assertTrue($formatter->fromFront($field, $attribute, true));
        $this->assertTrue($formatter->fromFront($field, $attribute, true));
        $this->assertTrue($formatter->fromFront($field, $attribute, 'true'));
        $this->assertTrue($formatter->fromFront($field, $attribute, 1));
        $this->assertTrue($formatter->fromFront($field, $attribute, '1'));
        $this->assertTrue($formatter->fromFront($field, $attribute, 'ok'));
        $this->assertTrue($formatter->fromFront($field, $attribute, 'on'));

        $this->assertFalse($formatter->fromFront($field, $attribute, false));
        $this->assertFalse($formatter->fromFront($field, $attribute, 'false'));
        $this->assertFalse($formatter->fromFront($field, $attribute, '0'));
        $this->assertFalse($formatter->fromFront($field, $attribute, 0));
        $this->assertFalse($formatter->fromFront($field, $attribute, ''));
    }
}
