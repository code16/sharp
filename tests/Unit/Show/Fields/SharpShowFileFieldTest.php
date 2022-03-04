<?php

namespace Code16\Sharp\Tests\Unit\Show\Fields;

use Code16\Sharp\Show\Fields\SharpShowFileField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowFileFieldTest extends SharpTestCase
{
    /** @test */
    public function we_can_define_a_file_field()
    {
        $field = SharpShowFileField::make('fileField')
            ->setLabel('test');

        $this->assertEquals([
            'key'          => 'fileField',
            'type'         => 'file',
            'label'        => 'test',
            'emptyVisible' => false,
        ], $field->toArray());
    }
}
