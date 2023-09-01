<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTextareaFieldTest extends SharpTestCase
{
    /** @test */
    public function only_default_values_are_set()
    {
        $formField = SharpFormTextareaField::make('text');

        $this->assertEquals(
            [
                'key' => 'text',
                'type' => 'textarea',
            ],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_row_count()
    {
        $formField = SharpFormTextareaField::make('text')
            ->setRowCount(5);

        $this->assertArraySubset(
            ['rows' => 5],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_cant_define_an_invalid_row_count()
    {
        $this->expectException(SharpFormFieldValidationException::class);

        SharpFormTextareaField::make('text')
            ->setRowCount(0)
            ->toArray();
    }

    /** @test */
    public function we_can_define_maxLength()
    {
        $formField = SharpFormTextareaField::make('text')
            ->setMaxLength(10);

        $this->assertArraySubset(
            ['maxLength' => 10],
            $formField->toArray(),
        );
    }
}
