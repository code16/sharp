<?php

namespace App\Sharp\Commands;

use App\User;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Facades\Storage;

class ExportUsersCommand extends EntityCommand
{
    public function label(): string
    {
        return "Export users as text file";
    }
    
    public function buildCommandConfig(): void
    {
        $this
            ->configureDescription("Download or stream a text file as response.");
    }

    public function execute(array $data = []): array
    {
        $fileContent = User::query()
            ->when($data["sample"] ?? false, function($query) {
                return $query->take(2);
            })
            ->get()
            ->map(function(User $user) {
                return implode(",", $user->toArray());
            })
            ->implode("\n");
        
        if($data["type"] == "streamDownload") {
            return $this->streamDownload($fileContent, "users.txt");
        }

        $filePath = "tmp/users " . now()->format("YmdHis") . ".txt";
        Storage::disk("local")->put($filePath, $fileContent);

        return $this->download($filePath, "users.txt", "local");
    }

    function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormCheckField::make("sample", "Download a file sample")
            )
            ->addField(
                SharpFormSelectField::make("type", [
                    "download" => "Download",
                    "streamDownload" => "Stream (no file storage)",
                ])
            );
    }
}