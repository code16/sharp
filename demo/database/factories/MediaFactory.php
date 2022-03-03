<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition()
    {
        return [
        ];
    }

    public function withFile($path)
    {
        return $this->state(function (array $attributes) use ($path) {
            $type = class_basename($attributes['model_type']);
            $modelId = $attributes['model_id'];
            $filename = basename($path);
            copy($path, storage_path("app/tmp/$filename"));
            Storage::disk('local')
                ->delete("/data/$type/$modelId/$filename");
            Storage::disk('local')
                ->copy("/tmp/$filename", "/data/$type/$modelId/$filename");

            return [
                'file_name' => "data/$type/$modelId/$filename",
            ];
        });
    }
}
