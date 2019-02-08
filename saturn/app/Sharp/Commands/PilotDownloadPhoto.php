<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PilotDownloadPhoto extends InstanceCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Download photo";
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     */
    public function execute($instanceId, array $data = []): array
    {
        if(! Storage::exists("photos/pilot-photo.jpg")) {
            UploadedFile::fake()->image('pilot-photo.jpg', 120, 120)->storeAs('photos', 'pilot-photo.jpg');
        }

        return $this->download("photos/pilot-photo.jpg", "pilot-$instanceId.jpg", "local");
    }
}
