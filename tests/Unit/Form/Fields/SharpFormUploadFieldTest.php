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
                'key' => 'file',
                'type' => 'upload',
                'compactThumbnail' => false,
                'transformable' => true,
                'transformKeepOriginal' => true,
                'shouldOptimizeImage' => false,
            ],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_maxFileSize()
    {
        $formField = SharpFormUploadField::make('file')
            ->setMaxFileSize(.5);

        $this->assertArraySubset(
            ['maxFileSize' => 0.5],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_compactThumbnail()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCompactThumbnail();

        $this->assertArraySubset(
            ['compactThumbnail' => true],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_transformable()
    {
        $formField = SharpFormUploadField::make('file')
            ->setTransformable(false);

        $this->assertArraySubset(
            ['transformable' => false],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_transformKeepOriginal_with_transformable()
    {
        $formField = SharpFormUploadField::make('file')
            ->setTransformable(true, false);

        $this->assertArraySubset(
            [
                'transformable' => true,
                'transformKeepOriginal' => false,
            ],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_fileFilter()
    {
        $formField = SharpFormUploadField::make('file')
            ->setFileFilter('jpg');

        $this->assertArraySubset(
            ['fileFilter' => ['.jpg']],
            $formField->toArray(),
        );

        $formField = SharpFormUploadField::make('file')
            ->setFileFilter('jpg, gif');

        $this->assertArraySubset(
            ['fileFilter' => ['.jpg', '.gif']],
            $formField->toArray(),
        );

        $formField = SharpFormUploadField::make('file')
            ->setFileFilter(['jpg', 'gif ']);

        $this->assertArraySubset(
            ['fileFilter' => ['.jpg', '.gif']],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_cropRatio()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCropRatio('16:9');

        $this->assertArraySubset(
            ['ratioX' => 16, 'ratioY' => 9],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_transformableFileTypes()
    {
        $formField = SharpFormUploadField::make('file')
            ->setCropRatio('16:9', ['jpg', 'jpeg']);

        $this->assertArraySubset(
            [
                'ratioX' => 16,
                'ratioY' => 9,
                'transformableFileTypes' => ['.jpg', '.jpeg'],
            ],
            $formField->toArray(),
        );

        $formField = SharpFormUploadField::make('file')
            ->setCropRatio('16:9', ['.jpg', '.jpeg']);

        $this->assertArraySubset(
            [
                'ratioX' => 16,
                'ratioY' => 9,
                'transformableFileTypes' => ['.jpg', '.jpeg'],
            ],
            $formField->toArray(),
        );
    }

    /** @test */
    public function we_can_define_shouldOptimizeImage()
    {
        $formField = SharpFormUploadField::make('file')
            ->shouldOptimizeImage();

        $this->assertArraySubset(
            [
                'shouldOptimizeImage' => true,
                'shouldConvertToJpg' => false,
            ],
            $formField->toArray(),
        );

        $formField2 = SharpFormUploadField::make('file')
            ->shouldOptimizeImage(shouldConvertToJpg: true);

        $this->assertArraySubset(
            [
                'shouldOptimizeImage' => true,
                'shouldConvertToJpg' => true,
            ],
            $formField2->toArray(),
        );

        $formField3 = SharpFormUploadField::make('file')
            ->shouldOptimizeImage(false);

        $this->assertArraySubset(
            [
                'shouldOptimizeImage' => false,
                'shouldConvertToJpg' => false,
            ],
            $formField3->toArray(),
        );

        $formField3 = SharpFormUploadField::make('file')
            ->shouldOptimizeImage(false);

        $this->assertArraySubset(
            [
                'shouldOptimizeImage' => false,
                'shouldConvertToJpg' => false,
            ],
            $formField3->toArray(),
        );

        $formField4 = SharpFormUploadField::make('file')
            ->shouldOptimizeImage(shouldOptimizeImage:false, shouldConvertToJpg: true);

        $this->assertArraySubset(
            [
                'shouldOptimizeImage' => false,
                'shouldConvertToJpg' => true,
            ],
            $formField4->toArray(),
        );
    }
}
