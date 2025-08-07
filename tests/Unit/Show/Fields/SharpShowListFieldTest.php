<?php

use Code16\Sharp\Show\Fields\SharpShowListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;

it('allows to define a list field', function () {
    $field = SharpShowListField::make('listField')
        ->addItemField(SharpShowTextField::make('textField'))
        ->setLabel('test');

    expect($field->toArray())->toEqual([
        'key' => 'listField',
        'type' => 'list',
        'label' => 'test',
        'emptyVisible' => false,
        'itemFields' => [
            'textField' => [
                'key' => 'textField',
                'emptyVisible' => false,
                'html' => true,
                'sanitize' => true,
                'type' => 'text',
            ],
        ],
    ]);
});
