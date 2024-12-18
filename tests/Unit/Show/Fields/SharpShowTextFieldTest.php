<?php

namespace Code16\Sharp\Tests\Unit\Show\Fields;

use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpShowTextFieldTest extends SharpTestCase
{
    /** @test */
    public function we_can_define_label()
    {
        $field = SharpShowTextField::make('textfield')
            ->setLabel('Label');

        $this->assertEquals(
            [
                'key' => 'textfield',
                'type' => 'text',
                'emptyVisible' => false,
                'html' => true,
                'label' => 'Label',
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_collapse_word_count()
    {
        $field = SharpShowTextField::make('textfield')
            ->collapseToWordCount(15);

        $this->assertEquals(
            [
                'key' => 'textfield',
                'type' => 'text',
                'emptyVisible' => false,
                'html' => true,
                'collapseToWordCount' => 15,
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_show_if_empty()
    {
        $field = SharpShowTextField::make('textfield')
            ->setShowIfEmpty(true);

        $this->assertEquals(
            [
                'key' => 'textfield',
                'type' => 'text',
                'emptyVisible' => true,
                'html' => true,
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_html()
    {
        $field = SharpShowTextField::make('textfield')
            ->setHtml(false);

        $this->assertEquals(
            [
                'key' => 'textfield',
                'type' => 'text',
                'emptyVisible' => false,
                'html' => false,
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_reset_collapse_word_count()
    {
        $field = SharpShowTextField::make('textfield')
            ->collapseToWordCount(15);

        $field->doNotCollapse();

        $this->assertEquals(
            [
                'key' => 'textfield',
                'type' => 'text',
                'emptyVisible' => false,
                'html' => true,
            ],
            $field->toArray(),
        );
    }

    /** @test */
    public function we_can_define_the_localized_attribute()
    {
        $field = SharpShowTextField::make('text')
            ->setLocalized(false);

        $this->assertArrayNotHasKey(
            'localized',
            $field->toArray(),
        );

        $field->setLocalized();

        $this->assertArraySubset(
            ['localized' => true],
            $field->toArray(),
        );
    }
}
