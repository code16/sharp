<?php

namespace Code16\Sharp\Dev\Commands;

use Illuminate\Console\Command;

class UpdateIdeJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sharp:update-ide-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update sharp ide.json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ideJsonPath = __DIR__.'/../../../ide.json';
        $ideJson = json_decode(file_get_contents($ideJsonPath), true);

        $ideJson['codeGenerations'] = collect([
            'Sharp Dashboard' => __DIR__.'/../../../src/Console/stubs/dashboard.stub',
            'Sharp EntityCommand' => __DIR__.'/../../../src/Console/stubs/entity-command.stub',
            'Sharp EntityCommand (form)' => __DIR__.'/../../../src/Console/stubs/entity-command.form.stub',
            'Sharp EntityCommand (wizard)' => __DIR__.'/../../../src/Console/stubs/entity-command.wizard.stub',
            'Sharp EntityList' => __DIR__.'/../../../src/Console/stubs/entity-list.stub',
            'Sharp EntityList (model)' => __DIR__.'/../../../src/Console/stubs/entity-list.model.stub',
            'Sharp Form' => __DIR__.'/../../../src/Console/stubs/form.stub',
            'Sharp Form (single)' => __DIR__.'/../../../src/Console/stubs/form.single.stub',
            'Sharp Form (model)' => __DIR__.'/../../../src/Console/stubs/form.model.stub',
            'Sharp InstanceCommand' => __DIR__.'/../../../src/Console/stubs/instance-command.stub',
            'Sharp InstanceCommand (form)' => __DIR__.'/../../../src/Console/stubs/instance-command.form.stub',
            'Sharp InstanceCommand (wizard)' => __DIR__.'/../../../src/Console/stubs/instance-command.wizard.stub',
            'Sharp Show' => __DIR__.'/../../../src/Console/stubs/show-page.stub',
            'Sharp Show (model)' => __DIR__.'/../../../src/Console/stubs/show-page.model.stub',
        ])
            ->map(function ($stubPath, $label) {
                return [
                    'name' => $label,
                    'id' => 'code16/sharp:'.str_replace('.stub', '', basename($stubPath)),
                    // 'parameters' => array_filter([
                    //     str_ends_with($stubPath, '.model.stub') ? [
                    //         'id' => 'code16/sharp:model',
                    //         'name' => 'Model',
                    //         'type' => 'input',
                    //         'variable' => 'MODEL',
                    //     ] : null,
                    // ]),
                    'files' => [
                        [
                            'template' => [
                                'path' => 'src/Console/'.str($stubPath)->after('src/Console/'),
                                'name' => '${INPUT_CLASS|className|upperCamelCase}.php',
                                'parameters' => [
                                    'DummyNamespace' => '${INPUT_FQN|namespace}',
                                    'DummyClass' => '${INPUT_CLASS|className|upperCamelCase}',
                                    // 'DummyModelClass' => '${MODEL}',
                                ],
                            ],
                        ],
                    ],
                ];
            })
            ->values();

        file_put_contents($ideJsonPath, json_encode($ideJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
