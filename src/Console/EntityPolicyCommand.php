<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class EntityPolicyCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:policy';
    protected $description = 'Create a new Policy class';
    protected $type = 'SharpPolicy';

    protected function buildClass($name)
    {
        $replace = [];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name),
        );
    }

    protected function getStub()
    {
        return __DIR__.'/stubs/policy.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [];
    }
}
