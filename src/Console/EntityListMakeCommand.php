<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class EntityListMakeCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:entity-list';
    protected $description = 'Create a new Entity List class';
    protected $type = 'SharpEntityList';

    protected function buildClass($name): string
    {
        if (! Str::endsWith($name, 'List')) {
            throw new \InvalidArgumentException('The Entity List name should end with "List"');
        }

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

    protected function getStub(): string
    {
        return $this->option('model')
            ? __DIR__.'/stubs/entity-list.model.stub'
            : __DIR__.'/stubs/entity-list.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return str($rootNamespace)
            ->append('\\Sharp\\')
            ->append(str($this->getNameInput())->substr(0, -4)->plural());
    }

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the list displays'],
        ];
    }
}
