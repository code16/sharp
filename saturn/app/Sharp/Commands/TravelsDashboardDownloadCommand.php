<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TravelsDashboardDownloadCommand extends DashboardCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Export as PDF";
    }

    /**
     * @param DashboardQueryParams $params
     * @param array $data
     * @return array
     */
    public function execute(DashboardQueryParams $params, array $data = []): array
    {
        if (!Storage::exists("pdf/travels-export.png")) {
            UploadedFile::fake()->image('travels-export.png', 120, 120)->storeAs('pdf', 'travels-export.png');
        }

        return $this->download(
            "pdf/travels-export.png",
            "travels-export-{$params->filterFor("period")}.png",
            "local"
        );
    }
}