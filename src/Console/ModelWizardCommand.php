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

        if ($fullModelClass && ! class_exists($fullModelClass)) {
            $this->call('make:model', ['name' => $fullModelClass]);
        }

        if ($listClass = $this->ask("List class name", "{$pluralModelClass}/{$modelClass}List")) {
            $this->call('sharp:make:list', ['name' => $listClass, '--model' => $inputModelClass]);
        }

        if ($formClass = $this->ask("Form class name", "{$pluralModelClass}/{$modelClass}Form")) {
            $this->call('sharp:make:form', ['name' => $formClass, '--model' => $inputModelClass]);
        }

        $this->info('Wizard complete!');
        $this->line('Add this to entities in `config/sharp.php`:');
        $this->comment("        '{$classSlug}' => [
            'list' => \\{$this->parseClassname($listClass, 'Sharp')}::class,
            'form' => \\{$this->parseClassname($formClass, 'Sharp')}::class,
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
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the list displays'],
        ];
    }
}
