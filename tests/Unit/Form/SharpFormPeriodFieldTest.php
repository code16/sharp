<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Carbon\Carbon;
use Code16\Sharp\Form\Fields\SharpFormPeriodField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormPeriodFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $defaultFormField = SharpFormPeriodField::make("date");

        $this->assertEquals([
                "key" => "date", "type" => "period",
                "startDate" => date("Y-m-d"),
                "displayFormat" => "yyyy-mm-dd"
            ], $defaultFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_min_and_max_date()
    {
        $date1 = Carbon::today();
        $date2 = Carbon::tomorrow();

        $dateFormField = SharpFormPeriodField::make("date")
            ->setMinDate($date1)
            ->setMaxDate($date2);

        $this->assertArraySubset([
                "minDate" => $date1->format("Y-m-d"),
                "maxDate" => $date2->format("Y-m-d"),
            ], $dateFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_a_start_date()
    {
        $date = Carbon::yesterday();

        $dateFormField = SharpFormPeriodField::make("date")
            ->setStartDate($date);

        $this->assertArraySubset(
            ["startDate" => $date->format("Y-m-d")],
            $dateFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_a_display_format()
    {
        $dateFormField = SharpFormPeriodField::make("date")
            ->setDisplayFormat("dd/mm/yyyy");

        $this->assertArraySubset(
            ["displayFormat" => "dd/mm/yyyy"],
            $dateFormField->toArray()
        );
    }

}