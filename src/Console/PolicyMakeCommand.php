<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class PolicyMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:policy';
    protected $description = 'Create a new Policy class';
    protected $type = 'Policy';

    protected function getStub()
    {
        return $this->option('entity-only') !== false
            ? __DIR__.'/stubs/policy.entity.stub'
            : __DIR__.'/stubs/policy.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['entity-only', 'eo', InputOption::VALUE_NONE, 'When policy only needs an entity rule'],
        ];
    }
}
