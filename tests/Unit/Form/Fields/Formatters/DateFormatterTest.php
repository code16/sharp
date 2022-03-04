<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Carbon\Carbon;
use Code16\Sharp\Form\Fields\Formatters\DateFormatter;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Tests\SharpTestCase;

class DateFormatterTest extends SharpTestCase
{
    /** @test */
    public function we_can_format_datetime_value_to_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasDate(true);
        $field->setHasTime(true);

        $this->assertEquals('2017-05-31 10:30:00', $formatter->toFront($field, '2017-05-31 10:30:00'));
        $this->assertEquals('2017-05-31 10:30:00', $formatter->toFront($field, Carbon::create(2017, 05, 31, 10, 30, 0)));
        $this->assertEquals('2017-05-31 10:30:00', $formatter->toFront($field, new \DateTime('2017-05-31 10:30:00')));
    }

    /** @test */
    public function we_can_format_date_only_value_to_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasDate(true);
        $field->setHasTime(false);

        $this->assertEquals('2017-05-31', $formatter->toFront($field, '2017-05-31'));
        $this->assertEquals('2017-05-31', $formatter->toFront($field, Carbon::create(2017, 05, 31)->startOfDay()));
        $this->assertEquals('2017-05-31', $formatter->toFront($field, new \DateTime('2017-05-31')));
    }

    /** @test */
    public function we_can_format_time_only_value_to_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasDate(false);
        $field->setHasTime(true);

        $this->assertEquals('10:30:00', $formatter->toFront($field, '10:30:00'));
        $this->assertEquals('10:30:00', $formatter->toFront($field, Carbon::create(2017, 05, 31, 10, 30, 00)));
        $this->assertEquals('10:30:00', $formatter->toFront($field, new \DateTime('10:30:00')));
    }

    /** @test */
    public function we_can_format_datetime_value_from_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasDate(true);
        $field->setHasTime(true);
        $attribute = 'attribute';

        $this->assertEquals('2017-05-31 10:30:00', $formatter->fromFront($field, $attribute, '2017-05-31 10:30:00'));
    }

    /** @test */
    public function we_can_format_date_only_value_from_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasDate(true);
        $field->setHasTime(false);
        $attribute = 'attribute';

        $this->assertEquals('2017-05-31', $formatter->fromFront($field, $attribute, '2017-05-31'));
        $this->assertEquals('2017-05-31', $formatter->fromFront($field, $attribute, '2017-05-31 10:30:00'));
    }

    /** @test */
    public function we_can_format_time_only_value_from_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasDate(false);
        $field->setHasTime(true);
        $attribute = 'attribute';

        $this->assertEquals('10:30:00', $formatter->fromFront($field, $attribute, '2017-05-31 10:30:00'));
        $this->assertEquals('10:30:00', $formatter->fromFront($field, $attribute, '10:30:00'));
    }

    /** @test */
    public function we_handle_timezone_from_front()
    {
        $formatter = new DateFormatter();
        $field = SharpFormDateField::make('date');
        $field->setHasTime(true);
        $attribute = 'attribute';

        config(['app.timezone' => 'Europe/Paris']); //GMT+2
        $this->assertEquals('2017-05-31 15:00:00', $formatter->fromFront($field, $attribute, '2017-05-31T13:00:00.000Z'));

        config(['app.timezone' => 'America/Montreal']); //GMT-4
        $this->assertEquals('2017-05-31 09:00:00', $formatter->fromFront($field, $attribute, '2017-05-31T13:00:00.000Z'));
    }
}
