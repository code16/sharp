<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Transformers;

use Code16\Sharp\Form\Eloquent\Transformers\EloquentFormUploadTransformer;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Utils\TestWithSharpUploadModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EloquentFormUploadTransformerTest extends SharpFormEloquentBaseTest
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
    function we_can_transform_a_single_upload()
    {
        $picturable = Picturable::create();
        $upload = $this->createSharpUploadModel($this->createImage(), $picturable);

        $transformer = new EloquentFormUploadTransformer();

        $this->assertEquals([
            "id" => $upload->id,
            "name" => $upload->file_name,
            "size" => (string)$upload->size,
            "thumbnail" => $upload->thumbnail(null, 150)
        ], $transformer->apply($picturable, "picture"));
    }

    /** @test */
    function we_can_transform_a_list_of_upload()
    {
        $picturable = Picturable::create();
        $upload = $this->createSharpUploadModel($this->createImage(), $picturable);
        $upload2 = $this->createSharpUploadModel($this->createImage(), $picturable);

        $transformer = new EloquentFormUploadTransformer();

        $this->assertEquals([[
            "file" => [
                "name" => $upload->file_name,
                "size" => (string)$upload->size,
                "thumbnail" => $upload->thumbnail(null, 150)
            ],
            "id" => $upload->id,
        ], [
            "file" => [
                "name" => $upload2->file_name,
                "size" => (string)$upload2->size,
                "thumbnail" => $upload2->thumbnail(null, 150)
            ],
            "id" => $upload2->id,
        ]], $transformer->apply($picturable, "pictures"));
    }

}

class Picturable extends Model
{
    public function picture()
    {
        return $this->morphOne(SharpUploadModel::class, "model");
    }

    public function pictures()
    {
        return $this->morphMany(SharpUploadModel::class, "model");
    }
}