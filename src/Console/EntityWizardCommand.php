<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class EntityWizardCommand extends Command
{
    protected $name = 'sharp:wizard';
    protected $description = 'Wizard to create a new Entity class with associated files (list, form, show page, policy)';

    public function handle()
    {
        $entityType = select(
            label: 'What is the type of your entity?',
            options: ['Classic', 'Single', 'Dashboard'],
            default: 'Classic',
        );

        switch ($entityType) {
            case 'Classic':
                $this->generateClassicEntity();
                break;
            case 'Single':
                $this->generateSingleEntity();
                break;
            case 'Dashboard':
                $this->generateDashboardEntity();
                break;
        }

        $this->components->info('Your entity and all related files have been created successfully.');
    }

    protected function generateDashboardEntity()
    {
        $sharpRootNamespace = $this->laravel->getNamespace() . 'Sharp';

        $name = text(
            label: 'What is the name of your dashboard?',
            placeholder: 'E.g. Activity',
            required: true,
            hint: 'A "DashboardEntity" suffix will be added automatically (E.g. ActivityDashboardEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $needsPolicy = confirm(
            label: 'Do you need a policy?',
            default: false,
        );

        Artisan::call('sharp:make:dashboard', [
            'name' => 'Dashboards\\' . $name . 'Dashboard',
        ]);

        $this->components->twoColumnDetail('Dashboard', $sharpRootNamespace . '\\Dashboards\\' . $name . 'Dashboard.php');

        if ($needsPolicy) {

            Artisan::call('sharp:make:policy', [
                'name' => 'Dashboards\\' . $name . 'DashboardPolicy',
                '--entity-only' => '',
            ]);

            $this->components->twoColumnDetail('Policy', $sharpRootNamespace . '\\Dashboards\\' . $name . 'DashboardPolicy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\' . $name . 'DashboardEntity',
            '--dashboard' => '',
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $sharpRootNamespace . '\\Entities\\' . $name . 'DashboardEntity.php');
    }

    protected function generateClassicEntity()
    {
        $sharpRootNamespace = $this->laravel->getNamespace() . 'Sharp';

        $name = text(
            label: 'What is the name of your entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'An "Entity" suffix will be added automatically (E.g. UserEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));
        $pluralName = Str::plural($name);

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
        $model = $modelPath . '\\' . Str::ucfirst(Str::camel($model));

        if (!class_exists($model)) {
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
            'name' => $pluralName . '\\' . $name . 'EntityList',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Entity list', $sharpRootNamespace . '\\' . $pluralName . '\\' . $name . 'EntityList.php');

        if (Str::contains($type, 'form')) {

            Artisan::call('sharp:make:validator', [
                'name' => $pluralName . '\\' . $name . 'Validator',
            ]);

            $this->components->twoColumnDetail('Validator', $sharpRootNamespace . '\\' . $pluralName . '\\' . $name . 'Validator.php');

            Artisan::call('sharp:make:form', [
                'name' => $pluralName . '\\' . $name . 'Form',
                '--model' => $model,
                '--validator' => '',
            ]);

            $this->components->twoColumnDetail('Form', $sharpRootNamespace . '\\' . $pluralName . '\\' . $name . 'Form.php');
        }

        if (Str::contains($type, 'show')) {

            Artisan::call('sharp:make:show-page', [
                'name' => $pluralName . '\\' . $name . 'Show',
            ]);

            $this->components->twoColumnDetail('Show page', $sharpRootNamespace . '\\' . $pluralName . '\\' . $name . 'Show.php');
        }

        if ($needsPolicy) {

            Artisan::call('sharp:make:policy', [
                'name' => $pluralName . '\\' . $name . 'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $sharpRootNamespace . '\\' . $pluralName . '\\' . $name . 'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\' . $name . 'Entity',
            '--label' => $label,
            ...(Str::contains($type, 'form') ? ['--form' => ''] : []),
            ...(Str::contains($type, 'show') ? ['--show' => ''] : []),
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $sharpRootNamespace . '\\Entities\\' . $name . 'Entity.php');
    }

    private function generateSingleEntity()
    {
        $sharpRootNamespace = $this->laravel->getNamespace() . 'Sharp';

        $name = text(
            label: 'What is the name of your entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'An "Entity" suffix will be added automatically (E.g. UserEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $label = text(
            label: 'What is the label of your entity?',
            placeholder: 'E.g. Administrators',
            required: true,
            hint: 'It will be displayed in the breadcrumb'
        );

        $needsPolicy = confirm(
            label: 'Do you need a policy?',
            default: false,
        );

        Artisan::call('sharp:make:validator', [
            'name' => $name . '\\' . $name . 'SingleValidator',
        ]);

        $this->components->twoColumnDetail('Validator', $sharpRootNamespace . '\\' . $name . '\\' . $name . 'SingleValidator.php');

        Artisan::call('sharp:make:form', [
            'name' => $name . '\\' . $name . 'SingleForm',
            '--single' => '',
            '--validator' => '',
        ]);

        $this->components->twoColumnDetail('Single form', $sharpRootNamespace . '\\' . $name . '\\' . $name . 'SingleForm.php');


        Artisan::call('sharp:make:show-page', [
            'name' => $name . '\\' . $name . 'SingleShow',
            '--single' => '',
        ]);

        $this->components->twoColumnDetail('Single show page', $sharpRootNamespace . '\\' . $name . '\\' . $name . 'SingleShow.php');

        if ($needsPolicy) {

            Artisan::call('sharp:make:policy', [
                'name' => $name . '\\' . $name . 'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $sharpRootNamespace . '\\' . $name . '\\' . $name . 'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\' . $name . 'Entity',
            '--label' => $label,
            '--single' => '',
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $sharpRootNamespace . '\\Entities\\' . $name . 'Entity.php');
    }
}
