<?php

namespace Code16\Sharp\Tests\Unit\Show\Fields;

use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowPictureFieldTest extends SharpTestCase
{
    /** @test */
    function we_can_define_a_picture_field()
    {
        $field = SharpShowPictureField::make("pictureField");

        $this->assertEquals([
            "key" => "pictureField",
            "type" => "picture",
        ], $field->toArray());
    }
}