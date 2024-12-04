<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateDbSnapshot extends Command
{
    protected $signature = 'e2e:update-db-snapshot';
    protected $description = 'Dump the DB for e2e testing';

    public function handle()
    {
        Artisan::call('migrate:fresh', ['--seed' => true], $this->output);
        Artisan::call('snapshot:create', ['name' => 'e2e-seed'], $this->output);
    }
}
