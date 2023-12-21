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
        $pluralEntityName = Str::plural($entityName);

        $sharpRootNamespace = $this->getDefaultNamespace(
            trim($this->rootNamespace(), '\\')
        );

        $replace = [
            'DummyEntitiesNamespace' => $sharpRootNamespace.'\Entities',
            'DummyDashboardClass' => $entityName,
            'DummyFullDashboardClass' => $sharpRootNamespace.'\\Dashboards\\'.$entityName,
            'DummyDashboardPolicyClass' => $entityName.'Policy',
            'DummyFullDashboardPolicyClass' => $sharpRootNamespace.'\\Dashboards\\'.$entityName.'Policy',
            'DummyEntityListClass' => $entityName.'EntityList',
            'DummyFullEntityListClass' => $sharpRootNamespace.'\\'.$pluralEntityName.'\\'.$entityName.'EntityList',
            'DummyShowClass' => $entityName.'Show',
            'DummyFullShowClass' => $sharpRootNamespace.'\\'.$pluralEntityName.'\\'.$entityName.'Show',
            'DummyFormClass' => $entityName.'Form',
            'DummyFullFormClass' => $sharpRootNamespace.'\\'.$pluralEntityName.'\\'.$entityName.'Form',
            'DummySingleShowClass' => $entityName.'SingleShow',
            'DummyFullSingleShowClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'SingleShow',
            'DummySingleFormClass' => $entityName.'SingleForm',
            'DummyFullSingleFormClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'SingleForm',
            'DummyPolicyClass' => $entityName.'Policy',
            'DummyFullPolicyClass' => $sharpRootNamespace.'\\'.$pluralEntityName.'\\'.$entityName.'Policy',
            'DummySinglePolicyClass' => $entityName.'Policy',
            'DummyFullSinglePolicyClass' => $sharpRootNamespace.'\\'.$entityName.'\\'.$entityName.'Policy',
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
        if ($this->option('dashboard') !== false) {
            return $this->option('policy') !== false
                ? __DIR__.'/stubs/entity.dashboard-policy.stub'
                : __DIR__.'/stubs/entity.dashboard.stub';
        }

        if ($this->option('single') !== false) {
            return $this->option('policy') !== false
                ? __DIR__.'/stubs/entity.single-policy.stub'
                : __DIR__.'/stubs/entity.single.stub';
        }

        if ($this->option('form') !== false && $this->option('show') !== false) {
            return $this->option('policy') !== false
                ? __DIR__.'/stubs/entity.list-form-show-policy.stub'
                : __DIR__.'/stubs/entity.list-form-show.stub';
        }

        if ($this->option('form') !== false) {
            return $this->option('policy') !== false
                ? __DIR__.'/stubs/entity.list-form-policy.stub'
                : __DIR__.'/stubs/entity.list-form.stub';
        }

        return $this->option('policy') !== false
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
            ['dashboard', 'da', InputOption::VALUE_NONE, 'Entity needs a dashboard view'],
            ['form', 'fo', InputOption::VALUE_NONE, 'Entity needs a form'],
            ['show', 'sh', InputOption::VALUE_NONE, 'Entity needs a show page'],
            ['policy', 'po', InputOption::VALUE_NONE, 'Entity needs a policy'],
            ['single', 'si', InputOption::VALUE_NONE, 'Entity is single'],
        ];
    }
}
