<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Eloquent\Formatters\ListFormatter;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class ListFormatterTest extends SharpFormEloquentBaseTest
{

    /** @test */
    function we_can_format_value()
    {
        $data = [
            [
                "name" => "John Wayne",
                "job" => "Actor"
            ], [
                "name" => "John Ford",
                "job" => "Director"
            ]
        ];

        $formatter = app(ListFormatter::class);

        $this->assertEquals($data, $formatter->format($data));
    }
}