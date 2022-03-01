<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Str;

class FieldWithDataLocalizationFormatterTest extends SharpTestCase
{
    /** @test */
    public function we_add_missing_locales_when_formatting_a_localized_text_value_from_front_in_a_text_field()
    {
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
    }

    /** @test */
    public function we_format_locales_when_formatting_a_localized_text_value_from_front_in_a_text_field()
    {
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
    }

    /** @test */
    public function we_stand_to_null_when_formatting_a_null_localized_text_value_from_front_in_a_text_field()
    {
        $this->assertEquals(
            null,
            (new TextFormatter())
                ->setDataLocalizations(["fr", "en", "es"])
                ->fromFront(
                    SharpFormTextField::make('md')->setLocalized(),
                    'attribute',
                    null,
                ),
        );
    }

    /** @test */
    public function we_add_missing_locales_when_formatting_a_localized_text_value_from_front_in_a_textarea_field()
    {
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
    }

    /** @test */
    public function we_format_locales_when_formatting_a_localized_text_value_from_front_in_a_textarea_field()
    {
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
    }

    /** @test */
    public function we_add_missing_locales_when_formatting_a_localized_text_value_from_front_in_an_editor_field()
    {
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
    }

    /** @test */
    public function we_format_locales_when_formatting_a_localized_text_value_from_front_in_an_editor_field()
    {
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
    }

    /** @test */
    public function we_stand_to_null_when_formatting_a_null_localized_text_value_from_front_in_an_editor_field()
    {
        $this->assertEquals(
            null,
            (new EditorFormatter())
                ->setDataLocalizations(["fr", "en", "es"])
                ->fromFront(
                    SharpFormEditorField::make('md')->setLocalized(),
                    'attribute',
                    null,
                ),
        );
    }
}
