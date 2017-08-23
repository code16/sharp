<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Tests\Fixtures\Person;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SharpUploadModelTest extends SharpFormEloquentBaseTest
{

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        Schema::create('sharp_upload_models', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('model');
            $table->string('model_key')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('disk')->default('local');
            $table->unsignedInteger('size')->nullable();
            $table->text('custom_properties')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
        });

        config(['filesystems.disks.local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ]]);

        config(['sharp.uploads.thumbnails_dir' => 'thumbnails']);

        File::deleteDirectory(storage_path("app/data"));
        File::deleteDirectory(public_path("thumbnails"));
    }

    /** @test */
    function all_thumbnails_are_destroyed_if_we_set_transformed_to_true()
    {
        $person = Person::create(["name" => "A"]);

        $file = $this->createImage();

        $upload = SharpUploadModel::create([
            "file_name" => $file,
            "size" => 120,
            "mime_type" => "image/png",
            "disk" => "local",
            "model_type" => Person::class,
            "model_id" => $person->id,
            "model_key" => "test"
        ]);

        $upload->thumbnail(100, 100);

        $this->assertTrue(file_exists(public_path("thumbnails/data/100-100/" . basename($file))));

        $upload->transformed = true;

        $this->assertFalse(file_exists(public_path("thumbnails/data/100-100/" . basename($file))));
    }

    /** @test */
    function a_thumbnail_is_created_when_asked()
    {
        $person = Person::create(["name" => "A"]);

        $file = $this->createImage();

        $upload = SharpUploadModel::create([
            "file_name" => $file,
            "size" => 120,
            "mime_type" => "image/png",
            "disk" => "local",
            "model_type" => Person::class,
            "model_id" => $person->id,
            "model_key" => "test"
        ]);

        $this->assertEquals(
            url("thumbnails/data/-150/" . basename($file)),
            $upload->thumbnail(null, 150)
        );

        $this->assertTrue(file_exists(public_path("thumbnails/data/-150/" . basename($file))));
    }

    private function createImage()
    {
        $file = (new FileFactory)->image("test.png", 600, 600);
        return $file->store("data");
    }
}
