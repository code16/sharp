<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class ResetTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saturn:reset-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all DB data and clean uploaded files';

    /** @var Filesystem */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('migrate:fresh', ["--seed" => true, "--force" => true]);

        $this->filesystem->cleanDirectory("storage/app/data");
        $this->filesystem->cleanDirectory("storage/app/tmp");
        $this->filesystem->cleanDirectory("public/thumbnails");
    }
}
