<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class EntityListMakeCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:entity-list';
    protected $description = 'Create a new Entity List class';
    protected $type = 'SharpEntityList';

    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    protected function getStub()
    {
        return $this->option('model')
            ? __DIR__.'/stubs/entity-list.model.stub'
            : __DIR__.'/stubs/entity-list.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the list displays'],
        ];
    }
}
