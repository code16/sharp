<?php

use Code16\Sharp\Show\Fields\SharpShowFileField;

it('allows to define a file field', function () {
    $field = SharpShowFileField::make('fileField')
        ->setLabel('test');

    expect($field->toArray())->toEqual([
        'key' => 'fileField',
        'type' => 'file',
        'label' => 'test',
        'emptyVisible' => false,
    ]);
});
