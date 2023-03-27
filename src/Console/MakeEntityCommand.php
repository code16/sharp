<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str;

class MakeEntityCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:entity';
    protected $description = 'Create a new Entity class';
    protected $type = 'SharpEntity';

    protected function buildClass($name)
    {
        $replace = [];

        $inputModelClass = $this->option('model') ?? $this->ask('Model class name');
        $pluralModelClass = Str::plural($inputModelClass);

        $replace = $this->buildModelReplacements($replace);
        $replace = array_merge($replace, [
            'DummyModelName' => ucfirst(class_basename($inputModelClass)),
            'DummyModelPluralName' => ucfirst(class_basename($pluralModelClass))
        ]);

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name),
        );
    }

    protected function getStub()
    {
        return __DIR__.'/stubs/entity.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the form handles'],
        ];
    }
}
