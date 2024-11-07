<?php

use Code16\Sharp\Form\Fields\Formatters\TagsFormatter;
use Code16\Sharp\Form\Fields\SharpFormTagsField;

it('allows to format ids to front', function () {
    $formatter = new TagsFormatter();
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals(
        [['id' => 1, 'label' => 'red']],
        $formatter->toFront($field, 1)
    );
    $this->assertEquals(
        [['id' => 1, 'label' => 'red'], ['id' => 2, 'label' => 'blue']],
        $formatter->toFront($field, [1, 2])
    );
});

it('allows to format objects to front', function () {
    $formatter = new TagsFormatter();
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals(
        [['id' => 1, 'label' => 'red']],
        $formatter->toFront($field, [(object) ['id' => 1, 'name' => 'A']]),
    );
    $this->assertEquals(
        [['id' => 1, 'label' => 'red'], ['id' => 2, 'label' => 'blue']],
        $formatter->toFront($field, [(object) ['id' => 1, 'name' => 'A'], (object) ['id' => 2, 'name' => 'B']]),
    );
});

it('allows to format arrays to front', function () {
    $formatter = new TagsFormatter();
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals(
        [['id' => 1, 'label' => 'red']],
        $formatter->toFront($field, [['id' => 1, 'name' => 'A']]),
    );
    $this->assertEquals(
        [['id' => 1, 'label' => 'red'], ['id' => 2, 'label' => 'blue']],
        $formatter->toFront($field, [['id' => 1, 'name' => 'A'], ['id' => 2, 'name' => 'B']]),
    );
});

it('allows to format objects to front with a defined id attribute', function () {
    $formatter = new TagsFormatter();
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setIdAttribute('number');

    $this->assertEquals(
        [['id' => 1, 'label' => 'red']],
        $formatter->toFront($field, [(object) ['number' => 1, 'name' => 'A']]),
    );
    $this->assertEquals(
        [['id' => 1, 'label' => 'red'], ['id' => 2, 'label' => 'blue']],
        $formatter->toFront($field, [(object) ['number' => 1, 'name' => 'A'], (object) ['number' => 2, 'name' => 'B']]),
    );
});

it('allows to format value from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals([['id' => 1]], $formatter->fromFront($field, $attribute, [['id' => 1]]));
    $this->assertEquals([['id' => 1], ['id' => 2]], $formatter->fromFront($field, $attribute, [['id' => 1], ['id' => 2]]));
});

it('we strip non configured values from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ]);

    $this->assertEquals([['id' => 1], ['id' => 2]], $formatter->fromFront(
        $field, $attribute, [['id' => 1], ['id' => 2], ['id' => 3]]),
    );
});

it('we handle creatable attribute from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setCreatable()
        ->setCreateAttribute('name');

    $this->assertEquals(
        [['id' => 1], ['id' => 2], ['id' => null, 'name' => 'green']],
        $formatter->fromFront(
            $field, $attribute, [['id' => 1], ['id' => 2], ['id' => null, 'label' => 'green']]),
        );
});

it('we strip null ids if creatable attribute is false from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setCreatable(false)
        ->setCreateAttribute('name');

    $this->assertEquals(
        [['id' => 1], ['id' => 2]],
        $formatter->fromFront(
            $field, $attribute, [['id' => 1], ['id' => 2], ['id' => null, 'label' => 'green']]),
        );
});

it('we handle id attribute from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setIdAttribute('number');

    $this->assertEquals([['number' => 1]], $formatter->fromFront($field, $attribute, [['id' => 1]]));
    $this->assertEquals([['number' => 1], ['number' => 2]], $formatter->fromFront($field, $attribute, [['id' => 1], ['id' => 2]]));
});

it('we handle id and creatable attribute from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setIdAttribute('number')
        ->setCreatable()
        ->setCreateAttribute('name');

    $this->assertEquals(
        [['number' => null, 'name' => 'green']],
        $formatter->fromFront($field, $attribute, [['id' => null, 'label' => 'green']]),
    );
});

it('we handle additional create attributes from front', function () {
    $formatter = new TagsFormatter();
    $attribute = 'attribute';
    $field = SharpFormTagsField::make('tags', [
        1 => 'red',
        2 => 'blue',
    ])
        ->setCreatable()
        ->setCreateAdditionalAttributes([
            'group' => 'test',
        ])
        ->setCreateAttribute('name');

    $this->assertEquals(
        [['id' => 1], ['id' => 2], ['id' => null, 'name' => 'green', 'group' => 'test']],
        $formatter->fromFront(
            $field, $attribute, [['id' => 1], ['id' => 2], ['id' => null, 'label' => 'green']],
        ),
    );
});
