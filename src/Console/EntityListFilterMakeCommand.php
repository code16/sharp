<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class EntityListFilterMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:entity-list-filter';
    protected $description = 'Create a new Entity List Filter class';
    protected $type = 'EntityListFilter';

    protected function getStub()
    {
        if ($this->option('required')) {
            return __DIR__.'/stubs/entity-list-filter.required.stub';
        }

        if ($this->option('multiple')) {
            return __DIR__.'/stubs/entity-list-filter.multiple.stub';
        }

        return __DIR__.'/stubs/entity-list-filter.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['required', 'r', InputOption::VALUE_NONE, 'Create a filter for which value is required'],
            ['multiple', 'm', InputOption::VALUE_NONE, 'Create a filter that can have multiple values'],
        ];
    }
}
