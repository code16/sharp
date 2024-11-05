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
            return $this->option('date-range')
                ? __DIR__.'/stubs/filters/entity-list-date-range-filter.required.stub'
                : __DIR__.'/stubs/filters/entity-list-select-filter.required.stub';
        }

        if ($this->option('date-range')) {
            return __DIR__.'/stubs/filters/entity-list-date-range-filter.stub';
        }

        if ($this->option('check')) {
            return __DIR__.'/stubs/filters/entity-list-check-filter.stub';
        }

        return $this->option('multiple')
            ? __DIR__.'/stubs/filters/entity-list-select-filter.multiple.stub'
            : __DIR__.'/stubs/filters/entity-list-select-filter.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['required', 'r', InputOption::VALUE_NONE, 'Create a filter for which value is required'],
            ['multiple', 'm', InputOption::VALUE_NONE, 'Create a select filter that can have multiple values'],
            ['date-range', 'd', InputOption::VALUE_NONE, 'Create a date-range filter'],
            ['check', 'c', InputOption::VALUE_NONE, 'Create a check filter'],
        ];
    }
}
