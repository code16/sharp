<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TravelsDashboardDownloadCommand extends DashboardCommand
{
    public function label(): string
    {
        return 'Export as PDF';
    }

    public function execute(array $data = []): array
    {
        if (!Storage::exists('pdf/travels-export.png')) {
            UploadedFile::fake()->image('travels-export.png', 120, 120)->storeAs('pdf', 'travels-export.png');
        }

        $append = $this->queryParams->filterFor('period')
            ? $this->queryParams->filterFor('period')['start']->format('Y-m-d')
            : 'all';

        return $this->download(
            'pdf/travels-export.png',
            "travels-export-{$append}.png",
            'local'
        );
    }
}
