<?php

namespace Database\Seeders;

use App\Models\TestTag;
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

        TestTag::factory()
            ->forEachSequence(
                ['label' => 'Tag 1'],
                ['label' => 'Tag 2'],
                ['label' => 'Tag 3'],
                ['label' => 'Tag 4'],
                ['label' => 'Tag 5'],
                ['label' => 'Tag 6'],
                ['label' => 'Tag 7'],
                ['label' => 'Tag 8'],
                ['label' => 'Tag 9'],
                ['label' => 'Tag 10'],
            )
            ->create();
    }
}
