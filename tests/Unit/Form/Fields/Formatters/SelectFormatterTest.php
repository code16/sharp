<?php

use Code16\Sharp\Form\Fields\Formatters\SelectFormatter;
use Code16\Sharp\Form\Fields\SharpFormSelectField;

it('allows to format value to front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals(1, $formatter->toFront($field, 1));
    $this->assertEquals(1, $formatter->toFront($field, [1, 2]));
});

it('allows to format a multiple value to front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple();

    $this->assertEquals([1, 2], $formatter->toFront($field, [1, 2]));
});

it('allows to format a multiple array value to front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple();

    $this->assertEquals([1, 2], $formatter->toFront($field, [
        ['id' => 1, 'label' => 'A'], ['id' => 2, 'label' => 'B'],
    ]));
});

it('allows to format a multiple object value to front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple();

    $this->assertEquals([1, 2], $formatter->toFront($field, [
        (object) ['id' => 1, 'label' => 'A'],
        (object) ['id' => 2, 'label' => 'B'],
    ]));
});

it('allows to format a multiple object with toArray value to front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple();

    $this->assertEquals([1, 2], $formatter->toFront($field, [
        new class()
        {
            public function toArray()
            {
                return ['id' => 1, 'label' => 'A'];
            }
        },
        new class()
        {
            public function toArray()
            {
                return ['id' => 2, 'label' => 'B'];
            }
        },
    ]));
});

it('allows to define idAttribute for a multiple array value to front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple()
        ->setIdAttribute('number');

    $this->assertEquals([1, 2], $formatter->toFront($field, [
        ['number' => 1, 'label' => 'A'], ['number' => 2, 'label' => 'B'],
    ]));
});

it('allows to format value from front', function () {
    $formatter = new SelectFormatter();
    $attribute = 'attribute';
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals(1, $formatter->fromFront($field, $attribute, 1));
    $this->assertEquals(1, $formatter->fromFront($field, $attribute, [1, 2]));
});

it('allows to format a multiple value from front', function () {
    $formatter = new SelectFormatter();
    $attribute = 'attribute';
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple();

    $this->assertEquals([['id' => 1], ['id' => 2]], $formatter->fromFront($field, $attribute, [1, 2]));
});

it('allows to define idAttribute for a multiple array value from front', function () {
    $formatter = new SelectFormatter();
    $field = SharpFormSelectField::make('select', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setMultiple()
        ->setIdAttribute('number');

    $this->assertEquals(
        [['number' => 1], ['number' => 2]],
        $formatter->fromFront($field, 'attribute', [1, 2]),
    );
});
