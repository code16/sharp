<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\SelectFormatter;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Tests\SharpTestCase;

class SelectFormatterTest extends SharpTestCase
{
    /** @test */
    public function we_can_format_value_to_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData());

        $this->assertEquals(1, $formatter->toFront($field, 1));
        $this->assertEquals(1, $formatter->toFront($field, [1, 2]));
    }

    /** @test */
    public function we_can_format_a_multiple_value_to_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple();

        $this->assertEquals([1, 2], $formatter->toFront($field, [1, 2]));
    }

    /** @test */
    public function we_can_format_a_multiple_array_value_to_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple();

        $this->assertEquals([1, 2], $formatter->toFront($field, [
            ['id' => 1, 'label' => 'A'], ['id' => 2, 'label' => 'B'],
        ]));
    }

    /** @test */
    public function we_can_format_a_multiple_object_value_to_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple();

        $this->assertEquals([1, 2], $formatter->toFront($field, [
            (object) ['id' => 1, 'label' => 'A'],
            (object) ['id' => 2, 'label' => 'B'],
        ]));
    }

    /** @test */
    public function we_can_format_a_multiple_object_with_toArray_value_to_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple();

        $this->assertEquals([1, 2], $formatter->toFront($field, [
            new class
            {
                public function toArray()
                {
                    return ['id' => 1, 'label' => 'A'];
                }
            },
            new class
            {
                public function toArray()
                {
                    return ['id' => 2, 'label' => 'B'];
                }
            },
        ]));
    }

    /** @test */
    public function we_can_define_idAttribute_for_a_multiple_array_value_to_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple()
            ->setIdAttribute('number');

        $this->assertEquals([1, 2], $formatter->toFront($field, [
            ['number' => 1, 'label' => 'A'], ['number' => 2, 'label' => 'B'],
        ]));
    }

    /** @test */
    public function we_can_format_value_from_front()
    {
        $formatter = new SelectFormatter;
        $attribute = 'attribute';
        $field = SharpFormSelectField::make('select', $this->getSelectData());

        $this->assertEquals(1, $formatter->fromFront($field, $attribute, 1));
        $this->assertEquals(1, $formatter->fromFront($field, $attribute, [1, 2]));
    }

    /** @test */
    public function we_can_format_a_multiple_value_from_front()
    {
        $formatter = new SelectFormatter;
        $attribute = 'attribute';
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple();

        $this->assertEquals([['id' => 1], ['id' => 2]], $formatter->fromFront($field, $attribute, [1, 2]));
    }

    /** @test */
    public function we_can_define_idAttribute_for_a_multiple_array_value_from_front()
    {
        $formatter = new SelectFormatter;
        $field = SharpFormSelectField::make('select', $this->getSelectData())
            ->setMultiple()
            ->setIdAttribute('number');

        $this->assertEquals(
            [['number' => 1], ['number' => 2]],
            $formatter->fromFront($field, 'attribute', [1, 2]),
        );
    }

    /**
     * @return array
     */
    protected function getSelectData()
    {
        return [
            1 => 'red',
            2 => 'blue',
        ];
    }
}
