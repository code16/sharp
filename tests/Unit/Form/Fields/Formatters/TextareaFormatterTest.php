<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldDataException;
use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Illuminate\Support\Str;

it('allows to format value to front', function () {
    $value = Str::random();

    expect((new TextareaFormatter())->toFront(SharpFormTextareaField::make('text'), $value))
        ->toEqual($value);
});

it('allows to format value from front', function () {
    $value = Str::random();

    expect((new TextareaFormatter())->fromFront(SharpFormTextareaField::make('text'), 'attr', $value))
        ->toEqual($value);
});

it('throws if localized value is invalid to front', function () {
    expect(fn () => (new TextareaFormatter())
        ->toFront(SharpFormTextareaField::make('text')->setLocalized(), 'test')
    )->toThrow(SharpFormFieldDataException::class);
    
    expect(fn () => (new TextareaFormatter())
        ->toFront(SharpFormTextareaField::make('text'), ['en' => 'test'])
    )->toThrow(SharpFormFieldDataException::class);
});

it('adds missing locales when formatting a localized text value from front in a textarea field', function () {
    $value = Str::random();
    
    expect(
        (new TextareaFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextareaField::make('text')->setLocalized(),
                'attribute',
                ['fr' => $value],
            )
    )
        ->toEqual(['fr' => $value, 'en' => null, 'es' => null]);
});

// edge case : we can't safely convert a string to a localized array so we pass the string through
it('returns a string when formatting a string text value from front in a localized text field', function () {
    $value = Str::random();
    
    expect(
        (new TextareaFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextareaField::make('md')->setLocalized(),
                'attribute',
                $value,
            )
    )->toEqual($value);
});
