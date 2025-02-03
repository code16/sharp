<?php

use Code16\Sharp\Show\Fields\SharpShowPictureField;

it('allow to define a picture field', function () {
    $field = SharpShowPictureField::make('pictureField');

    expect($field->toArray())->toEqual([
        'key' => 'pictureField',
        'type' => 'picture',
        'emptyVisible' => false,
    ]);
});
