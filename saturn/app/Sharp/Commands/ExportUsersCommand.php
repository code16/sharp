<?php

namespace App\Sharp\Commands;

use App\User;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Illuminate\Support\Facades\Storage;

class ExportUsersCommand extends EntityCommand
{
    public function label(): string
    {
        return 'Export users as text file';
    }

    public function execute(EntityListQueryParams $params, array $data = []): array
    {
        $filePath = 'tmp/users '.now()->format('YmdHis').'.txt';

        $users = ($data['sample'] ?? false)
            ? User::take(2)->get()
            : User::all();

        Storage::disk('local')->put(
            $filePath,
            $users
                ->map(function (User $user) {
                    return implode(',', $user->toArray());
                })
                ->implode("\n")
        );

        return $this->download($filePath, 'users.txt', 'local');
    }

    public function buildFormFields(): void
    {
        $this->addField(
            SharpFormCheckField::make('sample', 'Download a file sample')
        );
    }
}
