<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Carbon\Carbon;
use Code16\Sharp\Form\Eloquent\Formatters\DateFormatter;
use Code16\Sharp\Tests\SharpTestCase;

class DateFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_a_date_value()
    {
        $carbon = Carbon::create(2017, 05, 31)->startOfDay();

        $formatter = new DateFormatter;
        $this->assertEquals($carbon, $formatter->format('2017-05-31'));
    }

    /** @test */
    function we_can_format_a_date_time_value()
    {
        $carbon = Carbon::create(2017, 05, 31, 14, 30, 0);

        $formatter = new DateFormatter;
        $this->assertEquals($carbon, $formatter->format('2017-05-31 14:30'));
    }
}