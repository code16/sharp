<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition()
    {
        return [
        ];
    }

    public function withFile(string $srcFullPath, string $destRelativePath)
    {
        return $this->state(function (array $attributes) use ($srcFullPath, $destRelativePath) {
            if (! file_exists($dir = storage_path("app/data/$destRelativePath"))) {
                mkdir($dir, 0755, true);
            }
            copy($srcFullPath, sprintf('%s/%s', $dir, basename($srcFullPath)));

            return [
                'file_name' => sprintf("data/$destRelativePath/%s", basename($srcFullPath)),
            ];
        });
    }
}
