<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ShowPageMakeCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:show-page';
    protected $description = 'Create a new Show Page class';
    protected $type = 'SharpShow';

    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name),
        );
    }

    protected function getStub()
    {
        if ($this->option('single') !== false) {
            return __DIR__.'/stubs/show-page.single.stub';
        }

        if (! $this->option('model')) {
            return __DIR__.'/stubs/show-page.stub';
        }

        return __DIR__.'/stubs/show-page.model.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the show displays'],
            ['single', 's', InputOption::VALUE_NONE, 'Show page is single'],
        ];
    }
}
