<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

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
                "stepTime" => 30, "displayFormat" => "YYYY-MM-DD",
                "mondayFirst" => false, "language" => $this->app->getLocale()
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

        $this->assertArrayContainsSubset(
            ["key" => "date", "type" => "date", "hasDate" => true, "hasTime" => false],
            $dateFormField->toArray()
        );

        $this->assertArrayContainsSubset(
            ["key" => "date", "type" => "date", "hasTime" => true, "hasDate" => true],
            $dateTimeFormField->toArray()
        );

        $this->assertArrayContainsSubset(
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

        $this->assertArrayContainsSubset([
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

        $this->assertArrayContainsSubset(
            ["stepTime" => 45],
            $dateFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_monday_as_first_day_of_week()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setMondayFirst();

        $this->assertArrayContainsSubset(
            ["mondayFirst" => true],
            $dateFormField->toArray()
        );
    }

    /** @test */
    function we_can_define_a_display_format()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setDisplayFormat("DD/MM/YYYY");

        $this->assertArrayContainsSubset(
            ["displayFormat" => "DD/MM/YYYY"],
            $dateFormField->toArray()
        );
    }

    /** @test */
    function default_displayFormat_depends_on_date_time_configuration()
    {
        $dateFormField = SharpFormDateField::make("date")
            ->setHasDate();

        $dateTimeFormField = SharpFormDateField::make("date")
            ->setHasTime();

        $timeFormField = SharpFormDateField::make("date")
            ->setHasTime()
            ->setHasDate(false);

        $this->assertArrayContainsSubset(
            ["displayFormat" => "YYYY-MM-DD"],
            $dateFormField->toArray()
        );

        $this->assertArrayContainsSubset(
            ["displayFormat" => "YYYY-MM-DD HH:mm"],
            $dateTimeFormField->toArray()
        );

        $this->assertArrayContainsSubset(
            ["displayFormat" => "HH:mm"],
            $timeFormField->toArray()
        );
    }

}