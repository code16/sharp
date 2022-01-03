<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class FormMakeCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:form';
    protected $description = 'Create a new Form class';
    protected $type = 'SharpForm';

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
            ? __DIR__ . '/stubs/form.model.stub'
            : __DIR__ . '/stubs/form.stub';
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
