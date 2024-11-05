<?php

use Code16\Sharp\Form\Fields\Formatters\ListFormatter;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Illuminate\Support\Arr;

it('allows to format value to front', function () {
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('name'))
        ->addItemField(SharpFormTextField::make('job'));

    $data = getListFormatterData();

    $this->assertEquals($data, (new ListFormatter())->toFront($field, $data));
});

it('ignores non configured values when formatting to front', function () {
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('name'));

    $data = getListFormatterData();
    $expectedData = collect($data)
        ->map(fn ($item) => Arr::except($item, 'job'))
        ->all();

    $this->assertEquals(
        $expectedData,
        (new ListFormatter())->toFront($field, $data)
    );
});

it('allows to format value from front', function () {
    $attribute = 'attribute';
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('name'))
        ->addItemField(SharpFormTextField::make('job'));

    $data = getListFormatterData();

    $this->assertEquals($data, (new ListFormatter())->fromFront($field, $attribute, $data));
});

it('non configured values are ignored when formatting from front', function () {
    $attribute = 'attribute';
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('name'));

    $data = getListFormatterData();
    $expectedData = collect($data)
        ->map(fn ($item) => Arr::except($item, 'job'))
        ->all();

    $this->assertEquals($expectedData, (new ListFormatter())->fromFront($field, $attribute, $data));
});

it('executes formatter on each field', function () {
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('job')->setFormatter(new class() extends SharpFieldFormatter
        {
            public function toFront(SharpFormField $field, $value)
            {
                return strtoupper($value);
            }
            public function fromFront(SharpFormField $field, string $attribute, $value)
            {
                return strtolower($value);
            }
        }));

    $data = [
        ['id' => 1, 'job' => 'Developer'],
        ['id' => 2, 'job' => 'Designer'],
    ];
    expect((new ListFormatter())->toFront($field, $data))->toBe([
        ['id' => 1, 'job' => 'DEVELOPER'],
        ['id' => 2, 'job' => 'DESIGNER'],
    ]);
    expect((new ListFormatter())->fromFront($field, 'list', $data))->toBe([
        ['id' => 1, 'job' => 'developer'],
        ['id' => 2, 'job' => 'designer'],
    ]);
});

it('allows to configure the id attribute', function () {
    $formatter = new ListFormatter();
    $field = SharpFormListField::make('list')
        ->setItemIdAttribute('number')
        ->addItemField(SharpFormTextField::make('name'))
        ->addItemField(SharpFormTextField::make('job'));

    $data = collect(getListFormatterData())
        ->map(fn ($item) => array_merge(
            ['number' => $item['id']],
            Arr::except($item, 'id')
        ))
        ->all();

    $this->assertEquals($data, $formatter->toFront($field, $data));
});

it('non valuated values are initialized to null when formatting to front', function () {
    $formatter = new ListFormatter();
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('name'))
        ->addItemField(SharpFormTextField::make('job'));

    $this->assertEquals([
        ['id' => 1, 'name' => 'John Wayne', 'job' => null],
    ], $formatter->toFront($field, [
        ['id' => 1, 'name' => 'John Wayne'],
    ]));
});

it('allows to format sub value to front', function () {
    $formatter = new ListFormatter();
    $field = SharpFormListField::make('list')
        ->addItemField(SharpFormTextField::make('mother:name'));

    $data = [
        [
            'id' => 1,
            'mother' => [
                'name' => 'Jane',
            ],
        ], [
            'id' => 2,
            'mother' => [
                'name' => 'Alicia',
            ],
        ],
    ];

    $this->assertEquals([
        [
            'id' => 1,
            'mother:name' => 'Jane',
        ], [
            'id' => 2,
            'mother:name' => 'Alicia',
        ],
    ], $formatter->toFront($field, $data));
});

function getListFormatterData(): array
{
    return [
        [
            'id' => 1,
            'name' => fake()->name(),
            'job' => fake()->jobTitle(),
        ], [
            'id' => 2,
            'name' => fake()->name(),
            'job' => fake()->jobTitle(),
        ],
    ];
}
