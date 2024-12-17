<?php

use Code16\Sharp\Show\Fields\Formatters\ListFormatter;
use Code16\Sharp\Show\Fields\SharpShowListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;

it('allows to format value to front', function () {
    $field = SharpShowListField::make('list')
        ->addItemField(SharpShowTextField::make('name'))
        ->addItemField(SharpShowTextField::make('job'));

    expect((new ListFormatter())->toFront($field, [
        ['id' => 1, 'name' => 'John Doe', 'job' => 'Developer'],
        ['id' => 2, 'name' => 'Jane Doe', 'job' => 'Designer'],
    ]))
        ->toEqual([
            ['id' => 1, 'name' => ['text' => 'John Doe'], 'job' => ['text' => 'Developer']],
            ['id' => 2, 'name' => ['text' => 'Jane Doe'], 'job' => ['text' => 'Designer']],
        ]);
});
