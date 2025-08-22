<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldDataException;
use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormTextField;

it('allows to format value to front', function () {
    $value = Str::random();

    expect((new TextFormatter())->toFront(SharpFormTextField::make('text'), $value))
        ->toEqual($value);
});

it('allows to format value from front', function () {
    $value = Str::random();

    expect((new TextFormatter())->fromFront(SharpFormTextField::make('text'), 'attr', $value))
        ->toEqual($value);
});

it('throws if localized value is invalid to front', function () {
    expect(fn () => (new TextFormatter())
        ->toFront(SharpFormTextField::make('text')->setLocalized(), 'test')
    )->toThrow(SharpFormFieldDataException::class);

    expect(fn () => (new TextFormatter())
        ->toFront(SharpFormTextField::make('text'), ['en' => 'test'])
    )->toThrow(SharpFormFieldDataException::class);
});

it('adds missing locales when formatting a localized text value from front in a text field', function () {
    $value = Str::random();

    expect(
        (new TextFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextField::make('text')->setLocalized(),
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
        (new TextFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextField::make('md')->setLocalized(),
                'attribute',
                $value,
            )
    )->toEqual($value);
});

it('sets all locales to null when formatting a null localized text value from front in a text field', function () {
    expect(
        (new TextFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextField::make('md')->setLocalized(),
                'attribute',
                null,
            )
    )->toEqual(['fr' => null, 'en' => null, 'es' => null]);
});

it('sanitizes value from front if configured', function () {
    expect(
        (new TextFormatter())
            ->fromFront(
                SharpFormTextField::make('text'),
                'attribute',
                '<script>alert("XSS")</script>'
            )
    )
        ->toEqual('<script>alert("XSS")</script>');

    expect(
        (new TextFormatter())
            ->fromFront(
                SharpFormTextField::make('text')->setSanitizeHtml(),
                'attribute',
                '<script>alert("XSS")</script><img src="x" onerror="alert(\'XSS\')">'
            )
    )
        ->toEqual('<img src="x" />');
});
