<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads\Transformers;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Utils\TestWithSharpUploadModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SharpUploadModelFormAttributeTransformerTest extends SharpFormEloquentBaseTest
{
    use TestWithSharpUploadModel {
        getEnvironmentSetUp as protected traitGetEnvironmentSetUp;
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->traitGetEnvironmentSetUp($app);

        Schema::create('picturables', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    /** @test */
    public function we_can_transform_a_single_upload()
    {
        $picturable = Picturable::create();
        $upload = $this->createSharpUploadModel($this->createImage(), $picturable);

        $transformer = new SharpUploadModelFormAttributeTransformer();

        $this->assertEquals(
            [
                'id' => $upload->id,
                'name' => basename($upload->file_name),
                'path' => $upload->file_name,
                'disk' => 'local',
                'size' => $upload->size,
                'thumbnail' => $upload->thumbnail(800, 600),
            ],
            $transformer->apply('', $picturable, 'picture'),
        );
    }

    /** @test */
    public function we_can_transform_a_single_upload_with_transformations()
    {
        $picturable = Picturable::create();
        $upload = $this->createSharpUploadModel($this->createImage(), $picturable);
        $upload->filters = [
            'crop' => [
                'height' => .5,
                'width' => .75,
                'x' => .3,
                'y' => .34,
            ],
            'rotate' => [
                'angle' => 45,
            ],
        ];
        $upload->save();

        $transformer = new SharpUploadModelFormAttributeTransformer();

        $this->assertEquals(
            [
                'id' => $upload->id,
                'name' => basename($upload->file_name),
                'path' => $upload->file_name,
                'disk' => 'local',
                'size' => $upload->size,
                'thumbnail' => $upload->thumbnail(800, 600),
                'filters' => [
                    'crop' => [
                        'height' => .5,
                        'width' => .75,
                        'x' => .3,
                        'y' => .34,
                    ],
                    'rotate' => [
                        'angle' => 45,
                    ],
                ],
            ],
            $transformer->apply('', $picturable, 'picture'),
        );
    }

    /** @test */
    public function we_can_transform_a_list_of_upload()
    {
        $picturable = Picturable::create();
        $upload = $this->createSharpUploadModel($this->createImage(), $picturable);
        $upload2 = $this->createSharpUploadModel($this->createImage(), $picturable);

        $transformer = new SharpUploadModelFormAttributeTransformer();

        $this->assertEquals(
            [
                [
                    'file' => [
                        'name' => basename($upload->file_name),
                        'path' => $upload->file_name,
                        'disk' => 'local',
                        'size' => $upload->size,
                        'thumbnail' => $upload->thumbnail(800, 600),
                    ],
                    'id' => $upload->id,
                ], [
                    'file' => [
                        'name' => basename($upload2->file_name),
                        'path' => $upload2->file_name,
                        'disk' => 'local',
                        'size' => $upload2->size,
                        'thumbnail' => $upload2->thumbnail(800, 600),
                    ],
                    'id' => $upload2->id,
                ],
            ],
            $transformer->apply('', $picturable, 'pictures'),
        );
    }

    /** @test */
    public function we_can_transform_a_list_of_upload_with_transformations()
    {
        $picturable = Picturable::create();
        $upload = $this->createSharpUploadModel($this->createImage(), $picturable);
        $upload2 = $this->createSharpUploadModel($this->createImage(), $picturable);

        $filters = [
            'crop' => [
                'height' => .5,
                'width' => .75,
                'x' => .3,
                'y' => .34,
            ],
            'rotate' => [
                'angle' => 45,
            ],
        ];

        $upload->filters = $filters;
        $upload->save();

        $transformer = new SharpUploadModelFormAttributeTransformer();

        $this->assertEquals(
            [
                [
                    'file' => [
                        'name' => basename($upload->file_name),
                        'path' => $upload->file_name,
                        'disk' => 'local',
                        'size' => $upload->size,
                        'thumbnail' => $upload->thumbnail(800, 600),
                        'filters' => $filters,
                    ],
                    'id' => $upload->id,
                ], [
                    'file' => [
                        'name' => basename($upload2->file_name),
                        'path' => $upload2->file_name,
                        'disk' => 'local',
                        'size' => $upload2->size,
                        'thumbnail' => $upload2->thumbnail(800, 600),
                    ],
                    'id' => $upload2->id,
                ],
            ],
            $transformer->apply('', $picturable, 'pictures'),
        );
    }

    /** @test */
    public function we_can_fake_an_sharpUpload_and_transform_a_single_upload()
    {
        $file = $this->createImage();

        $uploadData = [
            'file_name' => $file,
            'size' => 120,
            'disk' => 'local',
            'filters' => [],
        ];

        $transformer = (new SharpUploadModelFormAttributeTransformer())->dynamicInstance();

        $this->assertEquals(
            [
                'id' => null,
                'name' => basename($file),
                'path' => $file,
                'disk' => 'local',
                'size' => 120,
                'thumbnail' => (new SharpUploadModel($uploadData))->thumbnail(800, 600),
                'filters' => [],
            ],
            $transformer->apply($uploadData, null, 'picture'),
        );
    }
}

class Picturable extends Model
{
    public function picture()
    {
        return $this->morphOne(SharpUploadModel::class, 'model');
    }

    public function pictures()
    {
        return $this->morphMany(SharpUploadModel::class, 'model');
    }
}
