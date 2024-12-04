<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UpdateDbSnapshot extends Command
{
    protected $signature = 'e2e:update-db-snapshot';
    protected $description = 'Dump the DB for e2e testing';

    public function handle()
    {
        DB::table('cache')->truncate();
        DB::table('sessions')->truncate();
        Artisan::call('snapshot:create', ['name' => 'e2e-seed'], $this->output);
    }
}
