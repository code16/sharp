<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class EntityWizardCommand extends Command
{
    protected $name = 'sharp:wizard';
    protected $description = 'Wizard to create a new Entity class with associated files (list, form, show page, policy)';

    public function handle()
    {
        $sharpRootNamespace = $this->laravel->getNamespace().'Sharp';

        $name = text(
            label: 'What is the name of your entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'Entity suffix will be added automatically.',
        );
        $name = Str::title($name);

        $modelPath = text(
            label: 'What is the path of the associated model?',
            default: 'App\\Models',
            required: true,
        );

        $model = text(
            label: 'What is the name of the associated model?',
            placeholder: 'E.g. User',
            required: true,
        );
        $model = $modelPath.'\\'.Str::title($model);

        if (! class_exists($model)) {
            $this->components->error(sprintf('Sorry the model class [%s] cannot be found', $model));

            return;
        }

        $label = text(
            label: 'What is the label of your entity?',
            placeholder: 'E.g. Administrators',
            required: true,
            hint: 'It will be displayed in the breadcrumb'
        );

        $type = select(
            label: 'What do you need with your entity?',
            options: ['List', 'List & form', 'List, form & show page'],
            default: 'List, form & show page',
        );

        $needsPolicy = confirm(
            label: 'Do you need a policy?',
            default: false,
        );

        Artisan::call('sharp:make:entity-list', [
            'name' => $name.'s\\'.$name.'EntityList',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Entity list', $sharpRootNamespace.'\\'.$name.'s\\'.$name.'EntityList.php');

        if (Str::contains($type, 'form')) {
            Artisan::call('sharp:make:validator', [
                'name' => $name.'s\\'.$name.'Validator',
            ]);

            $this->components->twoColumnDetail('Validator', $sharpRootNamespace.'\\'.$name.'s\\'.$name.'Validator.php');

            Artisan::call('sharp:make:form', [
                'name' => $name.'s\\'.$name.'Form',
                '--model' => $model,
                '--validator' => '',
            ]);

            $this->components->twoColumnDetail('Form', $sharpRootNamespace.'\\'.$name.'s\\'.$name.'Form.php');
        }

        if (Str::contains($type, 'show')) {
            Artisan::call('sharp:make:show-page', [
                'name' => $name.'s\\'.$name.'Show',
            ]);

            $this->components->twoColumnDetail('Show page', $sharpRootNamespace.'\\'.$name.'s\\'.$name.'Show.php');
        }

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => $name.'s\\'.$name.'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $sharpRootNamespace.'\\'.$name.'s\\'.$name.'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'Entity',
            '--label' => $label,
            ...(Str::contains($type, 'form') ? ['--form' => ''] : []),
            ...(Str::contains($type, 'show') ? ['--show' => ''] : []),
            ...($needsPolicy ? ['--policy' => ''] : []),

        ]);

        $this->components->twoColumnDetail('Entity', $sharpRootNamespace.'\\Entities\\'.$name.'Entity.php');

        $this->components->info('Your entity and all related files have been created successfully.');
    }
}
