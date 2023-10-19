<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class WizardCommand extends Command
{
    protected $name = 'sharp:wizard';
    protected $description = 'Wizard that help you create entities, commands or filters';

    public function handle()
    {
        $wizardType = select(
            label: 'What do you need?',
            options: ['A complete entity (with list, form, etc)', 'A command', 'A list filter'],
            default: 'A complete entity (with list, form, etc)',
        );

        switch ($wizardType) {
            default:
            case 'A complete entity (with list, form, etc)':
                $this->entityWizard();
                break;
            case 'A command':
                $this->commandWizard();
                break;
            case 'A list filter':
                $this->filterWizard();
                break;
        }
    }
    public function filterWizard()
    {
        $filterType = select(
            label: 'What is the type of the new filter?',
            options: ['Select', 'Date range', 'Check'],
            default: 'Select',
        );

        $isMultiple = false;

        if ($filterType === 'Select') {
            $isMultiple = confirm(
                label: 'Can the filter accept multiple values?',
                default: false,
            );
        }

        $isRequired = false;

        if ($filterType === 'Date range' || ($filterType === 'Select' && !$isMultiple)) {
            $allowEmptyValues = confirm(
                label: 'Can the filter accept empty value?',
            );
            $isRequired = !$allowEmptyValues;
        }

        $name = text(
            label: 'What is the name of your filter?',
            placeholder: 'E.g. ShippingState',
            required: true,
            hint: 'A "Filter" suffix will be added automatically (E.g. ShippingStateFilter.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = text(
            label: 'What is the name of the related sharp entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'We\'ll use it to find the right folder',
        );
        $entityName = Str::ucfirst(Str::camel($entityName));

        $filterPath = text(
            label: 'What is the path where the file should be created?',
            default: Str::plural($entityName) . '\\Filters',
            required: true,
        );

        Artisan::call('sharp:make:entity-list-filter', [
            'name' => $filterPath . '\\' . $name . 'Filter',
            ...($isRequired ? ['--required' => ''] : []),
            ...($isMultiple ? ['--multiple' => ''] : []),
            ...($filterType === 'Check' ? ['--check' => ''] : []),
            ...($filterType === 'Date range' ? ['--date-range' => ''] : []),
        ]);

        $this->components->twoColumnDetail(sprintf('%s filter', $filterType), $this->getSharpRootNamespace() . '\\' . $filterPath . '\\' . $name . 'Filter.php');

        $this->components->info('Your filter has been created successfully.');
    }

    public function commandWizard()
    {
        $commandType = select(
            label: 'What is the type of the new command?',
            options: ['Instance', 'Entity'],
            default: 'Instance',
        );

        $needsForm = confirm(
            label: 'Do you need a form inside the command?',
            default: false,
        );

        if ($needsForm) {
            $needsWizard = confirm(
                label: 'Do you need a wizard?',
                default: false,
                hint: 'A wizard is a multi-step form.',
            );
        }

        $name = text(
            label: 'What is the name of your command?',
            placeholder: 'E.g. SendResetPasswordEmail',
            required: true,
            hint: 'A "Command" suffix will be added automatically (E.g. SendResetPasswordEmailCommand.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = text(
            label: 'What is the name of the related sharp entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'We\'ll use it to find the right folder',
        );
        $entityName = Str::ucfirst(Str::camel($entityName));

        $commandPath = text(
            label: 'What is the path where the file should be created?',
            default: Str::plural($entityName) . '\\Commands',
            required: true,
        );

        $needsWizard = $needsWizard ?? false;

        Artisan::call(sprintf('sharp:make:%s-command', Str::lower($commandType)), [
            'name' => $commandPath . '\\' . $name . 'Command',
            ...(!$needsWizard && $needsForm ? ['--form' => ''] : []),
            ...($needsWizard ? ['--wizard' => ''] : []),
        ]);

        $this->components->twoColumnDetail(sprintf('%s command', $commandType), $this->getSharpRootNamespace() . '\\' . $commandPath . '\\' . $name . 'Command.php');

        $this->components->info('Your command has been created successfully.');
    }

    public function entityWizard()
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
            'name' => 'Dashboards\\'.$name.'Dashboard',
        ]);

        $this->components->twoColumnDetail('Dashboard', $this->getSharpRootNamespace().'\\Dashboards\\'.$name.'Dashboard.php');

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => 'Dashboards\\'.$name.'DashboardPolicy',
                '--entity-only' => '',
            ]);

            $this->components->twoColumnDetail('Policy', $this->getSharpRootNamespace().'\\Dashboards\\'.$name.'DashboardPolicy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'DashboardEntity',
            '--dashboard' => '',
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $this->getSharpRootNamespace().'\\Entities\\'.$name.'DashboardEntity.php');
    }

    protected function generateClassicEntity()
    {
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
        $model = $modelPath.'\\'.Str::ucfirst(Str::camel($model));

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
            'name' => $pluralName.'\\'.$name.'EntityList',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Entity list', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'EntityList.php');

        if (Str::contains($type, 'form')) {
            Artisan::call('sharp:make:validator', [
                'name' => $pluralName.'\\'.$name.'Validator',
            ]);

            $this->components->twoColumnDetail('Validator', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Validator.php');

            Artisan::call('sharp:make:form', [
                'name' => $pluralName.'\\'.$name.'Form',
                '--model' => $model,
                '--validator' => '',
            ]);

            $this->components->twoColumnDetail('Form', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Form.php');
        }

        if (Str::contains($type, 'show')) {
            Artisan::call('sharp:make:show-page', [
                'name' => $pluralName.'\\'.$name.'Show',
            ]);

            $this->components->twoColumnDetail('Show page', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Show.php');
        }

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => $pluralName.'\\'.$name.'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'Entity',
            '--label' => $label,
            ...(Str::contains($type, 'form') ? ['--form' => ''] : []),
            ...(Str::contains($type, 'show') ? ['--show' => ''] : []),
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $this->getSharpRootNamespace().'\\Entities\\'.$name.'Entity.php');
    }

    private function generateSingleEntity()
    {
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
            'name' => $name.'\\'.$name.'SingleValidator',
        ]);

        $this->components->twoColumnDetail('Validator', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'SingleValidator.php');

        Artisan::call('sharp:make:form', [
            'name' => $name.'\\'.$name.'SingleForm',
            '--single' => '',
            '--validator' => '',
        ]);

        $this->components->twoColumnDetail('Single form', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'SingleForm.php');

        Artisan::call('sharp:make:show-page', [
            'name' => $name.'\\'.$name.'SingleShow',
            '--single' => '',
        ]);

        $this->components->twoColumnDetail('Single show page', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'SingleShow.php');

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => $name.'\\'.$name.'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'Entity',
            '--label' => $label,
            '--single' => '',
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $this->getSharpRootNamespace().'\\Entities\\'.$name.'Entity.php');
    }

    private function getSharpRootNamespace()
    {
        return $this->laravel->getNamespace().'Sharp';
    }
}
