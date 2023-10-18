<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CommandWizardCommand extends Command
{
    protected $name = 'sharp:command:wizard';
    protected $description = 'Wizard to create a new sharp command';

    public function handle()
    {
        $entityType = select(
            label: 'What is the type of the new command?',
            options: ['Instance', 'Entity'],
            default: 'Instance',
        );

        $sharpRootNamespace = $this->laravel->getNamespace().'Sharp';

        $needsForm = confirm(
            label: 'Do you need a form inside the command?',
            default: false,
        );

        $name = text(
            label: 'What is the name of your command?',
            placeholder: 'E.g. SendResetPasswordEmail',
            required: true,
            hint: 'A "Command" suffix will be added automatically (E.g. SendResetPasswordEmailCommand.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = text(
            label: 'What is the name of your entity?',
            placeholder: 'E.g. User',
            required: true,
        );
        $entityName = Str::ucfirst(Str::camel($entityName));

        $commandPath = text(
            label: 'What is the path where the file should be created?',
            default: Str::plural($entityName).'\\Commands',
            required: true,
        );

        Artisan::call(sprintf('sharp:make:%s-command', Str::lower($entityType)), [
            'name' => $commandPath.'\\'.$name.'Command',
            ...($needsForm ? ['--with-form' => ''] : []),
        ]);

        $this->components->twoColumnDetail(sprintf('%s command', $entityType), $sharpRootNamespace.'\\'.$commandPath.'\\'.$name.'Command.php');

        $this->components->info('Your command has been created successfully.');
    }
}
