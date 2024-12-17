<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // TODO after editing this file, run:
    //  > php artisan migrate:fresh --seed
    //  > php artisan snapshot:create e2e-seed

    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.org',
        ]);
    }
}
