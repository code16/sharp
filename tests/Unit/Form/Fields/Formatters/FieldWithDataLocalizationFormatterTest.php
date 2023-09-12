<?php

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Illuminate\Support\Str;

it('adds missing locales when formatting a localized text value from front in a text field', function () {
    $value = Str::random();

    $this->assertEquals(
        ['fr' => $value, 'en' => null, 'es' => null],
        (new TextFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextField::make('text')->setLocalized(),
                'attribute',
                ['fr' => $value],
            ),
    );
});

it('formats locales when formatting a localized text value from front in a text field', function () {
    $value = Str::random();

    $this->assertEquals(
        ['fr' => null, 'en' => $value, 'es' => null],
        (new TextFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextField::make('md')->setLocalized(),
                'attribute',
                $value,
            ),
    );
});

it('sets all locales to null when formatting a null localized text value from front in a text field', function () {
    $this->assertEquals(
        ['fr' => null, 'en' => null, 'es' => null],
        (new TextFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextField::make('md')->setLocalized(),
                'attribute',
                null,
            ),
    );
});

it('adds missing locales when formatting a localized text value from front in a textarea field', function () {
    $value = Str::random();

    $this->assertEquals(
        ['fr' => $value, 'en' => null, 'es' => null],
        (new TextareaFormatter)
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextareaField::make('text')->setLocalized(),
                'attribute',
                ['fr' => $value],
            ),
    );
});

it('we format locales when formatting a localized text value from front in a textarea field', function () {
    $value = Str::random();

    $this->assertEquals(
        ['fr' => null, 'en' => $value, 'es' => null],
        (new TextareaFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormTextareaField::make('md')->setLocalized(),
                'attribute',
                $value,
            ),
    );
});

it('adds missing locales when formatting a localized text value from front in an editor field', function () {
    $value = Str::random();

    $this->assertEquals(
        ['fr' => $value, 'en' => null, 'es' => null],
        (new EditorFormatter)
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormEditorField::make('md')->setLocalized(),
                'attribute',
                ['text' => ['fr' => $value]],
            ),
    );
});

it('formats locales when formatting a localized text value from front in an editor field', function () {
    $value = Str::random();

    $this->assertEquals(
        ['fr' => null, 'en' => $value, 'es' => null],
        (new EditorFormatter)
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormEditorField::make('md')->setLocalized(),
                'attribute',
                ['text' => $value],
            ),
    );
});

it('stands to null when formatting a null localized text value from front in an editor field', function () {
    $this->assertEquals(
        null,
        (new EditorFormatter())
            ->setDataLocalizations(['fr', 'en', 'es'])
            ->fromFront(
                SharpFormEditorField::make('md')->setLocalized(),
                'attribute',
                null,
            ),
    );
});