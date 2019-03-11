<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpWidget;
use Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException;
use Code16\Sharp\Tests\SharpTestCase;

class SharpWidgetTest extends SharpTestCase
{
    /** @test */
    function we_must_define_a_key()
    {
        $this->expectException(SharpWidgetValidationException::class);
        SomeTestWidget::make("")->toArray();
    }

    /** @test */
    function we_must_define_a_type()
    {
        $this->expectException(SharpWidgetValidationException::class);
        SomeTestWidget::make("type", "")->toArray();
    }

    /** @test */
    function returned_array_contains_key_and_type()
    {
        $widget = SomeTestWidget::make("name", "test");

        $this->assertArrayContainsSubset(
            ["key" => "name", "type" => "test"],
            $widget->toArray()
        );
    }

    /** @test */
    function returned_array_does_not_contain_null_attributes()
    {
        $widget = SomeTestWidget::make("name");

        $this->assertEquals(
            ["key" => "name", "type" => "test"],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_title()
    {
        $widget = SomeTestWidget::make("name")
            ->setTitle("title");

        $this->assertArrayContainsSubset(
            ["title" => "title"],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_an_entity_link()
    {
        $widget = SomeTestWidget::make("name")
            ->setLink("entity");

        $this->assertArrayContainsSubset(
            ["link" => route("code16.sharp.list", "entity")],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_an_instance_link()
    {
        $widget = SomeTestWidget::make("name")
            ->setLink("entity", 1);

        $this->assertArrayContainsSubset(
            ["link" => route("code16.sharp.edit", ["entity", 1])],
            $widget->toArray()
        );
    }

}

class SomeTestWidget extends SharpWidget
{
    public static function make(string $key, $type = "test")
    {
        return new static($key, $type);
    }

    public function toArray(): array
    {
        return parent::buildArray([]);
    }
}