<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormUploadFieldTest extends SharpTestCase
{
    /** @test */
    public function only_default_values_are_set()
    {
        $formField = SharpFormUploadField::make('file');

        $this->assertEquals(
            [
                'key'                 => 'file',
                'type'                => 'upload',
                'compactThumbnail'    => false,
                'croppable'           => true,
                'shouldOptimizeImage' => false,
            ],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_maxFileSize()
    {
        $formField = SharpFormUploadField::make('file')
            ->setMaxFileSize(.5);

        $this->assertArraySubset(
            ['maxFileSize' => 0.5],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_compactThumbnail()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCompactThumbnail();

        $this->assertArraySubset(
            ['compactThumbnail' => true],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_croppable()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCroppable(false);

        $this->assertArraySubset(
            ['croppable' => false],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_fileFilter()
    {
        $formField = SharpFormUploadField::make('file')
            ->setFileFilter('jpg');

        $this->assertArraySubset(
            ['fileFilter' => ['.jpg']],
            $formField->toArray()
        );

        $formField = SharpFormUploadField::make('file')
            ->setFileFilter('jpg, gif');

        $this->assertArraySubset(
            ['fileFilter' => ['.jpg', '.gif']],
            $formField->toArray()
        );

        $formField = SharpFormUploadField::make('file')
            ->setFileFilter(['jpg', 'gif ']);

        $this->assertArraySubset(
            ['fileFilter' => ['.jpg', '.gif']],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_cropRatio()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCropRatio('16:9');

        $this->assertArraySubset(
            ['ratioX' => 16, 'ratioY' => 9],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_croppableFileTypes()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCropRatio('16:9', ['jpg', 'jpeg']);

        $this->assertArraySubset(
            ['ratioX' => 16, 'ratioY' => 9, 'croppableFileTypes' => ['.jpg', '.jpeg']],
            $formField->toArray()
        );

        $formField = SharpFormUploadField::make('file')
            ->setCropRatio('16:9', ['.jpg', '.jpeg']);

        $this->assertArraySubset(
            ['ratioX' => 16, 'ratioY' => 9, 'croppableFileTypes' => ['.jpg', '.jpeg']],
            $formField->toArray()
        );
    }

    /** @test */
    public function we_can_define_shouldOptimizeImage()
    {
        $formField = SharpFormUploadField::make('file')
            ->shouldOptimizeImage();

        $this->assertArraySubset(
            ['shouldOptimizeImage' => true],
            $formField->toArray()
        );

        $formField2 = SharpFormUploadField::make('file')
            ->shouldOptimizeImage(false);

        $this->assertArraySubset(
            ['shouldOptimizeImage' => false],
            $formField2->toArray()
        );
    }
}
