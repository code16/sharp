<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class EntityWizardCommand extends Command
{

    protected $name = 'sharp:model-wizard';
    protected $description = 'Create a new Entity Wizard';

    public function handle()
    {

        $inputModelClass = $this->option('model') ?? $this->ask('Model class name');
        $pluralModelClass = Str::plural($inputModelClass);
        $fullModelClass = $this->parseClassname($inputModelClass, 'Models');
        $modelClass = class_basename($fullModelClass);
        $classSlug = Str::snake(class_basename($pluralModelClass));

        $config = collect();

        $this->call('make:model', ['name' => $fullModelClass]);

        $entityClass = $this->ask('Entity class name', "Entities/{$modelClass}Entity");
        $this->call('sharp:make:entity', ['name' => $entityClass, '--model' => $fullModelClass]);

        $listClass = $this->ask('List class name', "{$pluralModelClass}/{$modelClass}List");
        $this->call('sharp:make:entity-list', ['name' => $listClass, '--model' => $fullModelClass]);
        $config->push(['list' => "\\{$this->parseClassname($listClass, 'Sharp')}::class"]);

        $formClass = $this->ask('Form class name', "{$pluralModelClass}/{$modelClass}Form");
        $this->call('sharp:make:form', ['name' => $formClass, '--model' => $fullModelClass]);
        $config->push(['form' => "\\{$this->parseClassname($formClass, 'Sharp')}::class"]);

        if ($this->option('policy') || $this->confirm('Would you like to generate a policy class for this model?')) {
            $policyClass = $this->ask('Policy class name', "{$pluralModelClass}/{$modelClass}Policy");
            $this->call('sharp:make:policy', ['name' => $policyClass, '--model' => $fullModelClass]);
            $config->push(['policy' => "\\{$this->parseClassname($policyClass, 'Sharp')}::class"]);
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
            $namespace = $rootNamespace.($additionalNamespace ? trim(str_replace('/', '\\', $additionalNamespace), '\\').'\\' : '');
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
