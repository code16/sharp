<?php

namespace Code16\Sharp\Console;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ModelWizardCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharp:model-wizard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a model and all the classes needed to manage it in the Sharp admin';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inputModelClass = $this->option('model') ?? $this->ask('Model class name');
        $pluralModelClass = Str::plural($inputModelClass);
        $fullModelClass = $this->parseClassname($inputModelClass);
        $modelClass = class_basename($fullModelClass);
        $classSlug = Str::snake(class_basename($pluralModelClass));

        $config = collect();

        $this->call('make:model', ['name' => $fullModelClass]);

        $listClass = $this->ask("List class name", "{$pluralModelClass}/{$modelClass}List");
        $this->call('sharp:make:list', ['name' => $listClass, '--model' => $inputModelClass]);
        $config->push(['list' => "\\{$this->parseClassname($listClass, 'Sharp')}::class"]);

        $formClass = $this->ask("Form class name", "{$pluralModelClass}/{$modelClass}Form");
        $this->call('sharp:make:form', ['name' => $formClass, '--model' => $inputModelClass]);
        $config->push(['form' => "\\{$this->parseClassname($formClass, 'Sharp')}::class"]);

        if ($this->option('policy') || $this->confirm('Would you like to generate a policy class for this model?')) {
            $policyClass = $this->ask('Policy class name', "{$pluralModelClass}/{$modelClass}Policy");
            $this->call('sharp:make:policy', ['name' => $policyClass, '--model' => $inputModelClass]);
            $config->push(['policy' => "\\{$this->parseClassname($policyClass, 'Sharp')}::class"]);
        }

        if ($this->option('validator') || $this->confirm('Would you like to generate a validator class for this model?')) {
            $validatorClass = $this->ask('Validator class name', "{$pluralModelClass}/{$modelClass}Validator");
            $this->call('sharp:make:validator', ['name' => $validatorClass]);
            $config->push(['validator' => "\\{$this->parseClassname($validatorClass, 'Sharp')}::class"]);
        }

        $this->info('Wizard complete!');
        $this->line('Add this to entities in `config/sharp.php`:');
        $configString = $config->collapse()->map(function ($class, $key) {
            return "'{$key}' => {$class},";
        })->implode("\n            ");
        $this->comment("        '{$classSlug}' => [
            {$configString}
        ],");
    }

    /**
     * Get the fully-qualified class name.
     *
     * @param  string  $class
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseClassname($class, $additionalNamespace = null)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $class)) {
            throw new InvalidArgumentException('Class name contains invalid characters.');
        }

        $class = trim(str_replace('/', '\\', $class), '\\');

        if (! Str::startsWith($class, $rootNamespace = $this->laravel->getNamespace())) {
            $namespace = $rootNamespace . ($additionalNamespace ? trim(str_replace('/', '\\', $additionalNamespace), '\\') . '\\' : '');
            $class = $namespace.$class;
        }

        return $class;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the list displays'],
            ['policy', null, InputOption::VALUE_NONE, 'Create a policy for the model'],
            ['validator', null, InputOption::VALUE_NONE, 'Create a validator for the model'],
        ];
    }
}
