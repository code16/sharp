<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormEditorFieldTest extends SharpTestCase
{
    /** @test */
    public function only_default_values_are_set()
    {
        $formField = SharpFormEditorField::make('text');

        $this->assertEquals(
            [
                'key'       => 'text',
                'type'      => 'editor',
                'minHeight' => 200,
                'toolbar'   => [
                    SharpFormEditorField::B, SharpFormEditorField::I, SharpFormEditorField::SEPARATOR,
                    SharpFormEditorField::UL, SharpFormEditorField::SEPARATOR, SharpFormEditorField::A,
                ],
                'innerComponents' => [
                    'upload' => [
                        'maxFileSize'   => 2,
                        'transformable' => true,
                        'fileFilter'    => ['.jpg', '.jpeg', '.gif', '.png'],
                    ],
                ],
                'markdown' => false,
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_height()
    {
        $formField = SharpFormEditorField::make('text')
            ->setHeight(50);

        $this->assertArraySubset(
            ['minHeight' => 50, 'maxHeight' => 50],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_height_with_maxHeight()
    {
        $formField = SharpFormEditorField::make('text');

        $this->assertArraySubset(
            ['minHeight' => 50, 'maxHeight' => 100],
            $formField->setHeight(50, 100)->toArray()
        );

        $this->assertArraySubset(
            ['minHeight' => 50],
            $formField->setHeight(50, 0)->toArray()
        );
    }

    /** @test */
    public function we_can_define_upload_configuration()
    {
        $formField = SharpFormEditorField::make('text')
            ->setMaxFileSize(50);

        $this->assertArraySubset(
            [
                'innerComponents' => [
                    'upload' => [
                        'maxFileSize'   => 50,
                        'transformable' => true,
                    ],
                ],
            ],
            $formField->toArray()
        );

        $formField->setCropRatio('16:9');

        $this->assertArraySubset(
            [
                'innerComponents' => [
                    'upload' => [
                        'maxFileSize'   => 50,
                        'transformable' => true,
                        'ratioX'        => 16,
                        'ratioY'        => 9,
                    ],
                ],
            ],
            $formField->toArray()
        );

        $formField->setFileFilter(['jpg', 'pdf']);

        $this->assertArraySubset(
            [
                'innerComponents' => [
                    'upload' => [
                        'maxFileSize'   => 50,
                        'ratioX'        => 16,
                        'ratioY'        => 9,
                        'transformable' => true,
                        'fileFilter'    => ['.jpg', '.pdf'],
                    ],
                ],
            ],
            $formField->toArray()
        );

        $formField->setTransformable(false);

        $this->assertArraySubset(
            [
                'innerComponents' => [
                    'upload' => [
                        'maxFileSize'   => 50,
                        'ratioX'        => 16,
                        'ratioY'        => 9,
                        'transformable' => false,
                        'fileFilter'    => ['.jpg', '.pdf'],
                    ],
                ],
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_toolbar()
    {
        $formField = SharpFormEditorField::make('text')
            ->setToolbar([
                SharpFormEditorField::UPLOAD,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::UL,
            ]);

        $this->assertArraySubset(
            ['toolbar' => [
                SharpFormEditorField::UPLOAD,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::UL,
            ]],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_hide_toolbar()
    {
        $formField = SharpFormEditorField::make('text')
            ->setHeight(50)
            ->hideToolbar();

        $this->assertArrayNotHasKey('toolbar', $formField->toArray());
    }

    /** @test */
    public function we_can_define_markdown_as_content_renderer()
    {
        // These configs are globally set in the config
        config()->set('sharp.markdown_editor', [
            'tight_lists_only' => true,
            'nl2br'            => true,
        ]);

        $formField = SharpFormEditorField::make('text')
            ->setHeight(50)
            ->setRenderContentAsMarkdown();

        $this->assertArraySubset(
            [
                'markdown'       => true,
                'tightListsOnly' => true,
                'nl2br'          => true,
            ],
            $formField->toArray()
        );
    }
}
