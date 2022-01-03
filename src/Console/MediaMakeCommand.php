<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MediaMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:media';
    protected $description = 'Create the media model for sharp uploads';
    protected $type = 'Media model';

    protected function getStub()
    {
        return __DIR__.'/stubs/media.stub';
    }

    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('table')) {
            $replace = $this->replaceTableName($replace);
        } else {
            $replace = $this->removeTableProperty($replace);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function replaceTableName(array $replace)
    {
        return array_merge($replace, [
            'DummyTable' => $this->option('table'),
        ]);
    }

    protected function removeTableProperty(array $replace)
    {
        return array_merge($replace, [
            "    protected \$table = 'DummyTable';\n" => '',
        ]);
    }

    protected function getOptions()
    {
        return [
            ['table', 't', InputOption::VALUE_REQUIRED, 'Database table name'],
        ];
    }
}
