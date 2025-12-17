<?php

namespace Code16\Sharp\Dev\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TypescriptGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sharp:typescript-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update typescript types';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('ziggy:generate', [
            '--types-only' => true,
        ], $this->output);

        file_put_contents(
            $ziggyOutput = base_path(config('ziggy.output.path')),
            str(file_get_contents($ziggyOutput))
                ->replaceMatches('/("name": "filterKey"),\s+"required": true/', '$1')
        );

        Artisan::call('typescript:transform', [], $this->output);
    }
}
