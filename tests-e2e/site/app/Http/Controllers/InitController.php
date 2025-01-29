<?php

namespace App\Http\Controllers;

use App\Data\SeedParametersData;
use App\Models\User;
use Database\Seeders\InitSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class InitController extends Controller
{
    public function __invoke()
    {
        Artisan::call('snapshot:load', [
            'name' => 'e2e-seed',
        ]);

        Storage::disk('local')->deleteDirectory('data');

        Storage::disk(sharp()->config()->get('uploads.tmp_disk'))
            ->deleteDirectory(sharp()->config()->get('uploads.tmp_dir'));

        Storage::disk(sharp()->config()->get('uploads.thumbnails_disk'))
            ->deleteDirectory(sharp()->config()->get('uploads.thumbnails_dir'));

        if (request()->input('login')) {
            $user = User::where('email', 'test@example.org')->firstOrFail();

            auth()->login($user);
        }

        $seedParameters = new SeedParametersData(...request()->input('seed', []));

        (new InitSeeder())->run($seedParameters);

        return 'init ok';
    }
}
