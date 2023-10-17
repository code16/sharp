<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class EntityMakeCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:entity';
    protected $description = 'Create a new Entity class';
    protected $type = 'SharpEntity';

    protected function buildClass($name)
    {
        if (! Str::endsWith($name, 'Entity')) {
            throw new \InvalidArgumentException('The entity name should end with "Entity"');
        }

        $entityName = class_basename(substr($name, 0, -6));

        $sharpRootNamespace = $this->getDefaultNamespace(
            trim($this->rootNamespace(), '\\')
        );

        $replace = [
            'DummyEntitiesNamespace' => $sharpRootNamespace.'\Entities',
            'DummyEntityListClass' => $entityName.'EntityList',
            'DummyFullEntityListClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'EntityList',
            'DummyShowClass' => $entityName.'Show',
            'DummyFullShowClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'Show',
            'DummyFormClass' => $entityName.'Form',
            'DummyFullFormClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'Form',
            'DummyPolicyClass' => $entityName.'Policy',
            'DummyFullPolicyClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'Policy',
            ...($this->option('label') ? [
                'My dummy label' => $this->option('label'),
            ] : []),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name),
        );
    }

    protected function getStub()
    {
        if ($this->option('form') && $this->option('show')) {
            return $this->option('policy')
                ? __DIR__.'/stubs/entity.list-form-show-policy.stub'
                : __DIR__.'/stubs/entity.list-form-show.stub';
        }

        if ($this->option('form')) {
            return $this->option('policy')
                ? __DIR__.'/stubs/entity.list-form-policy.stub'
                : __DIR__.'/stubs/entity.list-form.stub';
        }

        return $this->option('policy')
            ? __DIR__.'/stubs/entity.list-policy.stub'
            : __DIR__.'/stubs/entity.list.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['label', 'la', InputOption::VALUE_REQUIRED, 'The label of the entity'],
            ['form', 'fo', InputOption::VALUE_NONE, 'Entity needs a form'],
            ['show', 'sh', InputOption::VALUE_NONE, 'Entity needs a show page'],
            ['policy', 'po', InputOption::VALUE_NONE, 'Entity needs a policy'],
        ];
    }
}
