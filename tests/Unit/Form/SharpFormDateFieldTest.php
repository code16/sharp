<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormDateFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $defaultFormField = SharpFormDateField::make("date");

        $this->assertEquals([
                "key" => "date", "type" => "date",
                "hasDate" => true, "hasTime" => false,
                "minTime" => '00:00', "maxTime" => '23:59',
                "stepTime" => 30, "displayFormat" => "yyyy-mm-dd",
                "mondayFirst" => false
            ], $defaultFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_hasDate_and_hasTime()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setHasDate();

        $dateTimeFormField = SharpFormDateField::make("date")
            ->setHasTime();

        $timeFormField = SharpFormDateField::make("date")
            ->setHasTime()
            ->setHasDate(false);

        $this->assertArraySubset(
            ["key" => "date", "type" => "date", "hasDate" => true, "hasTime" => false],
            $dateFormField->toArray()
        );

        $this->assertArraySubset(
            ["key" => "date", "type" => "date", "hasTime" => true, "hasDate" => true],
            $dateTimeFormField->toArray()
        );

        $this->assertArraySubset(
            ["key" => "date", "type" => "date", "hasTime" => true, "hasDate" => false],
            $timeFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_min_and_max_time()
    {
        $dateTimeFormField = SharpFormDateField::make("date")
            ->setMinTime(8)
            ->setMaxTime(20, 30);

        $this->assertArraySubset([
                "minTime" => "08:00",
                "maxTime" => "20:30",
            ], $dateTimeFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_a_step_time()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setStepTime(45);

        $this->assertArraySubset(
            ["stepTime" => 45],
            $dateFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_monday_as_first_day_of_week()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setMondayFirst();

        $this->assertArraySubset(
            ["mondayFirst" => true],
            $dateFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_a_display_format()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setDisplayFormat("dd/mm/yyyy");

        $this->assertArraySubset(
            ["displayFormat" => "dd/mm/yyyy"],
            $dateFormField->toArray()
        );
    }

}