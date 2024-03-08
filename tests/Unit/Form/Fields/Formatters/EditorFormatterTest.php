<?php

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('allows to format a text value to front', function () {
    $formatter = new EditorFormatter;
    $field = SharpFormEditorField::make('md');
    $value = Str::random()."\n\n".Str::random();

    $this->assertEquals(
        [
            'text' => $value,
        ],
        $formatter->toFront($field, $value),
    );
});

it('allows to format a text value from front', function () {
    $value = Str::random();

    $this->assertEquals(
        $value,
        (new EditorFormatter)->fromFront(
            SharpFormEditorField::make('md'),
            'attribute',
            ['text' => $value],
        ),
    );
});

it('allows to format a unicode text value from front', function () {
    // This test was created to demonstrate preg_replace failure
    // without the unicode modifier
    $value = '<p>ąężółść</p>';

    $this->assertEquals(
        $value,
        (new EditorFormatter)->fromFront(
            SharpFormEditorField::make('md'),
            'attribute',
            ['text' => $value],
        ),
    );
});
